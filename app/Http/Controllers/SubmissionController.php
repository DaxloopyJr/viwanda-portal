<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dataset;
use App\Models\Submission;
use App\Models\SubmissionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('submissions.view');

        $query = Submission::with(['institution', 'dataset', 'submitter'])->latest();
        $this->scopeToInstitution($query);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($period = $request->query('period')) {
            $query->where('reporting_period', $period);
        }
        if ($institution = $request->query('institution')) {
            $query->whereHas('institution', fn ($q) => $q->where('code', $institution));
        }

        return view('submissions.index', [
            'submissions' => $query->paginate(15)->withQueryString(),
            'statuses' => Submission::STATUSES,
            'periods' => Submission::select('reporting_period')->distinct()->orderByDesc('reporting_period')->pluck('reporting_period'),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('submissions.create');

        $datasets = Dataset::where('is_active', true)
            ->when(auth()->user()->isInstitutionUser(), fn ($q) => $q->where('institution_id', auth()->user()->institution_id))
            ->with('institution')->orderBy('code')->get();

        $dataset = $request->query('dataset') ? Dataset::where('code', $request->query('dataset'))->firstOrFail() : null;

        return view('submissions.create', compact('datasets', 'dataset'));
    }

    public function store(Request $request)
    {
        $this->authorize('submissions.create');

        $data = $request->validate([
            'dataset_id' => ['required', 'exists:datasets,id'],
            'reporting_period' => ['required', 'string', 'max:20'],
            'records' => ['required', 'array', 'min:1'],
        ]);

        $dataset = Dataset::findOrFail($data['dataset_id']);
        $this->authorizeDataset($dataset);

        $rules = $dataset->validationRules('records.*.');
        $validator = Validator::make($request->all(), $rules);

        $submission = DB::transaction(function () use ($data, $dataset, $validator, $request) {
            $submission = Submission::create([
                'reference' => Submission::nextReference(),
                'transaction_reference' => $request->input('transaction_reference'),
                'institution_id' => $dataset->institution_id,
                'dataset_id' => $dataset->id,
                'reporting_period' => $data['reporting_period'],
                'channel' => 'portal',
                'status' => 'draft',
                'submitted_by' => auth()->id(),
            ]);

            $row = 0;
            foreach ($data['records'] as $record) {
                $row++;
                if (! array_filter($record, fn ($v) => $v !== null && $v !== '')) {
                    continue; // skip entirely empty rows
                }
                SubmissionRecord::create([
                    'submission_id' => $submission->id,
                    'row_number' => $row,
                    'data' => $record,
                ]);
            }
            $submission->update(['records_count' => $submission->records()->count()]);

            return $submission;
        });

        $this->validateSubmission($submission);
        AuditLog::record('submission.created', $submission);

        return redirect()->route('submissions.show', $submission)
            ->with('success', "Draft submission {$submission->reference} saved. Review it, then submit for validation.");
    }

    public function show(Submission $submission)
    {
        $this->authorizeView($submission);
        $submission->load(['institution', 'dataset', 'submitter', 'reviewer', 'records']);
        $audits = AuditLog::where('auditable_type', Submission::class)
            ->where('auditable_id', $submission->id)->with('user')->latest()->get();

        return view('submissions.show', compact('submission', 'audits'));
    }

    public function edit(Submission $submission)
    {
        $this->authorizeEdit($submission);
        $submission->load(['dataset', 'records']);

        return view('submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorizeEdit($submission);

        $data = $request->validate([
            'reporting_period' => ['required', 'string', 'max:20'],
            'records' => ['required', 'array', 'min:1'],
        ]);

        $dataset = $submission->dataset;
        $validator = Validator::make($request->all(), $dataset->validationRules('records.*.'));

        DB::transaction(function () use ($submission, $data, $validator) {
            $old = ['status' => $submission->status, 'records_count' => $submission->records_count];
            $submission->records()->delete();
            $row = 0;
            foreach ($data['records'] as $record) {
                $row++;
                if (! array_filter($record, fn ($v) => $v !== null && $v !== '')) {
                    continue;
                }
                SubmissionRecord::create([
                    'submission_id' => $submission->id,
                    'row_number' => $row,
                    'data' => $record,
                ]);
            }
            $submission->update([
                'reporting_period' => $data['reporting_period'],
                'records_count' => $submission->records()->count(),
            ]);
            AuditLog::record('submission.updated', $submission, $old, $submission->only(['status', 'records_count']));
        });

        $this->validateSubmission($submission->fresh());

        return redirect()->route('submissions.show', $submission)->with('success', 'Submission updated and re-validated.');
    }

    public function destroy(Submission $submission)
    {
        $this->authorize('submissions.delete-own');
        $this->authorizeEdit($submission);
        abort_unless($submission->status === 'draft', 403, 'Only draft submissions can be deleted.');

        AuditLog::record('submission.deleted', $submission, $submission->only(['reference', 'status']));
        $submission->delete();

        return redirect()->route('submissions.index')->with('success', 'Draft submission deleted.');
    }

    /**
     * Officer submits a draft/returned submission into the institutional approval chain
     * (draft/returned_officer -> internal_review). Ministry-side users skip the internal
     * chain and submit straight to Ministry review.
     */
    public function submit(Submission $submission)
    {
        $this->authorizeEdit($submission);
        abort_if($submission->records_count === 0, 422, 'Add at least one record before submitting.');

        $this->validateSubmission($submission);
        if ($submission->fresh()->validation_errors) {
            return back()->with('error', 'Validation failed. Correct the highlighted records and submit again.');
        }

        $old = $submission->status;
        $viaInternalChain = auth()->user()->can('submissions.submit-internal');

        $submission->update([
            'status' => $viaInternalChain ? 'internal_review' : 'submitted',
            'submitted_at' => $viaInternalChain ? null : now(),
            'review_comments' => null,
        ]);
        AuditLog::record(
            $viaInternalChain ? 'submission.submitted_internal' : 'submission.submitted',
            $submission, ['status' => $old], ['status' => $submission->status]
        );

        return back()->with('success', $viaInternalChain
            ? "Submission {$submission->reference} sent to the supervisor for internal review."
            : "Submission {$submission->reference} sent for Ministry review.");
    }

    /** CSV upload into a new draft submission. */
    public function upload(Request $request)
    {
        $this->authorize('submissions.create');

        $data = $request->validate([
            'dataset_id' => ['required', 'exists:datasets,id'],
            'reporting_period' => ['required', 'string', 'max:20'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $dataset = Dataset::findOrFail($data['dataset_id']);
        $this->authorizeDataset($dataset);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        abort_unless($header, 422, 'The uploaded file is empty.');

        $columns = array_map(fn ($h) => trim($h), $header);
        $expected = array_column($dataset->fields, 'name');
        $missing = array_diff($expected, $columns);
        abort_if($missing, 422, 'Missing columns: '.implode(', ', $missing).'. Download the CSV template for the correct format.');

        $submission = DB::transaction(function () use ($handle, $columns, $dataset, $data, $request) {
            $submission = Submission::create([
                'reference' => Submission::nextReference(),
                'institution_id' => $dataset->institution_id,
                'dataset_id' => $dataset->id,
                'reporting_period' => $data['reporting_period'],
                'channel' => 'upload',
                'status' => 'draft',
                'submitted_by' => auth()->id(),
            ]);

            $row = 0;
            while (($line = fgetcsv($handle)) !== false) {
                $row++;
                $record = array_combine($columns, array_map(fn ($v) => trim((string) $v), $line));
                if (! array_filter($record, fn ($v) => $v !== '')) {
                    continue;
                }
                SubmissionRecord::create([
                    'submission_id' => $submission->id,
                    'row_number' => $row,
                    'data' => $record,
                ]);
            }
            fclose($handle);
            $submission->update(['records_count' => $submission->records()->count()]);

            return $submission;
        });

        $this->validateSubmission($submission);
        AuditLog::record('submission.uploaded', $submission);

        return redirect()->route('submissions.show', $submission)
            ->with('success', "File uploaded into draft {$submission->reference}. Review validation results, then submit.");
    }

    /** CSV template for a dataset. */
    public function template(Dataset $dataset)
    {
        $this->authorize('submissions.create');
        $columns = array_column($dataset->fields, 'name');

        return response(implode(',', $columns)."\n", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$dataset->code.'-template.csv"',
        ]);
    }

    /** Validate every record against the dataset schema; store row-level errors. */
    private function validateSubmission(Submission $submission): void
    {
        $rules = $submission->dataset->validationRules();
        $failures = [];

        foreach ($submission->records as $record) {
            $validator = Validator::make($record->data, $rules);
            $errors = $validator->fails() ? $validator->errors()->toArray() : null;
            $record->update(['errors' => $errors]);
            if ($errors) {
                $failures[$record->row_number] = $errors;
            }
        }

        $submission->update(['validation_errors' => $failures ?: null]);
    }

    private function scopeToInstitution($query): void
    {
        $user = auth()->user();
        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        }
    }

    private function authorizeView(Submission $submission): void
    {
        $this->authorize('submissions.view');
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $submission->institution_id !== $user->institution_id, 403);
    }

    private function authorizeEdit(Submission $submission): void
    {
        $this->authorize('submissions.edit-own');
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $submission->institution_id !== $user->institution_id, 403);
        abort_unless($submission->isEditable(), 403, 'Only draft or returned submissions can be edited.');
    }

    private function authorizeDataset(Dataset $dataset): void
    {
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $dataset->institution_id !== $user->institution_id, 403);
        abort_unless($dataset->is_active, 403, 'This dataset is not active.');
    }
}
