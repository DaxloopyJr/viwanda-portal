<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Consumer;
use App\Models\Dataset;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('submissions.view');

        return view('submissions.index', [
            'statuses' => Submission::STATUSES,
            'periods' => Submission::select('reporting_period')->distinct()->orderByDesc('reporting_period')->pluck('reporting_period'),
        ]);
    }

    /**
     * Batch submission wizard: pick the reporting period first, then tick the
     * catalogue datasets to report, then enter records for each — one batch.
     */
    public function create(Request $request)
    {
        $this->authorize('submissions.create');

        $user = auth()->user();
        $datasets = $this->scopedDatasets();
        $periods = SubmissionPeriod::forUser($user);
        $consumers = Consumer::forUser($user);
        $preselect = $request->query('dataset'); // dataset code deep-link

        return view('submissions.create', compact('datasets', 'periods', 'consumers', 'preselect'));
    }

    /** Store a batch: one draft submission per selected dataset, one batch reference. */
    public function store(Request $request)
    {
        $this->authorize('submissions.create');

        $data = $request->validate([
            'reporting_period' => ['required', 'string', 'max:40'],
            'consumers' => ['nullable', 'array'],
            'consumers.*' => ['string', 'max:60'],
            'datasets' => ['required', 'array', 'min:1'],
            'datasets.*.id' => ['required', 'distinct', 'exists:datasets,id'],
            'datasets.*.records' => ['required', 'array', 'min:1'],
        ]);

        // Validate every dataset's rows against its own schema before writing anything.
        $datasets = collect();
        $errors = [];
        foreach ($data['datasets'] as $i => $entry) {
            $dataset = Dataset::findOrFail($entry['id']);
            $this->authorizeDataset($dataset);
            $datasets->push($dataset);

            $validator = Validator::make(
                ['records' => $entry['records']],
                $dataset->validationRules('records.*.')
            );
            foreach ($validator->errors()->toArray() as $key => $messages) {
                $errors["datasets.$i.$key"] = array_map(
                    fn ($m) => "[{$dataset->code}] $m", $messages
                );
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $batchReference = count($data['datasets']) > 1 ? Submission::nextBatchReference() : null;
        $consumers = $data['consumers'] ?? null;

        $submissions = DB::transaction(function () use ($data, $datasets, $batchReference, $consumers, $request) {
            $created = [];
            foreach ($data['datasets'] as $i => $entry) {
                $dataset = $datasets[$i];
                $submission = Submission::create([
                    'reference' => Submission::nextReference(),
                    'batch_reference' => $batchReference,
                    'transaction_reference' => $request->input('transaction_reference'),
                    'institution_id' => $dataset->institution_id,
                    'dataset_id' => $dataset->id,
                    'reporting_period' => $data['reporting_period'],
                    'channel' => 'portal',
                    'consumers' => $consumers,
                    'status' => 'draft',
                    'submitted_by' => auth()->id(),
                ]);

                $row = 0;
                foreach ($entry['records'] as $record) {
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
                $this->validateSubmission($submission);
                AuditLog::record('submission.created', $submission);
                $created[] = $submission;
            }

            return $created;
        });

        $first = $submissions[0];
        $message = $batchReference
            ? "Batch {$batchReference} saved: ".count($submissions).' dataset drafts created for '.$data['reporting_period'].'. Review and submit them together.'
            : "Draft submission {$first->reference} saved. Review it, then submit for validation.";

        return redirect()->route('submissions.show', $first)->with('success', $message);
    }

    public function show(Submission $submission)
    {
        $this->authorizeView($submission);
        $submission->load(['institution', 'dataset.ministryDepartment', 'submitter', 'reviewer', 'records']);
        $audits = AuditLog::where('auditable_type', Submission::class)
            ->where('auditable_id', $submission->id)->with('user')->latest()->get();
        $batch = $submission->batch_reference ? $submission->batchSiblings() : collect();

        return view('submissions.show', compact('submission', 'audits', 'batch'));
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
            'reporting_period' => ['required', 'string', 'max:40'],
            'records' => ['required', 'array', 'min:1'],
        ]);

        $dataset = $submission->dataset;

        DB::transaction(function () use ($submission, $data, $dataset) {
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
     * Officer submits a draft/returned submission into the approval chain. When the
     * submission belongs to a batch, every editable batch member goes together.
     * Institutional officers enter the internal chain (internal_review); ministry
     * department officers submit straight to the ministry reviewer (submitted).
     */
    public function submit(Submission $submission)
    {
        $this->authorizeEdit($submission);

        $targets = $submission->batchSiblings()->filter(fn (Submission $s) => $s->isEditable());
        abort_if($targets->isEmpty(), 422, 'Nothing to submit.');

        $failed = [];
        foreach ($targets as $target) {
            if ($target->records_count === 0) {
                $failed[] = "{$target->reference}: no records";
                continue;
            }
            $this->validateSubmission($target);
            if ($target->fresh()->validation_errors) {
                $failed[] = "{$target->reference}: validation errors";
            }
        }
        if ($failed) {
            return back()->with('error', 'Cannot submit: '.implode('; ', $failed).'. Correct the highlighted records and submit again.');
        }

        $viaInternalChain = auth()->user()->can('submissions.submit-internal');
        $to = $viaInternalChain ? 'internal_review' : 'submitted';

        foreach ($targets as $target) {
            $old = $target->status;
            $target->update([
                'status' => $to,
                'submitted_at' => $viaInternalChain ? null : now(),
                'review_comments' => null,
            ]);
            AuditLog::record(
                $viaInternalChain ? 'submission.submitted_internal' : 'submission.submitted',
                $target, ['status' => $old], ['status' => $to]
            );
        }

        $count = $targets->count();
        $label = $submission->batch_reference ? "batch {$submission->batch_reference} ({$count} datasets)" : $submission->reference;

        return back()->with('success', $viaInternalChain
            ? "Submission {$label} sent to the supervisor for internal review."
            : "Submission {$label} sent for Ministry review.");
    }

    /** CSV upload into a new draft submission. */
    public function upload(Request $request)
    {
        $this->authorize('submissions.create');

        $data = $request->validate([
            'dataset_id' => ['required', 'exists:datasets,id'],
            'reporting_period' => ['required', 'string', 'max:40'],
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

        $submission = DB::transaction(function () use ($handle, $columns, $dataset, $data) {
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

    /** Datasets the current user may report against. */
    private function scopedDatasets()
    {
        $user = auth()->user();

        return Dataset::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('institution_id', $user->institution_id))
            ->when($user->isMinistryDepartmentUser(), fn ($q) => $q->where('department_id', $user->department_id))
            ->with(['institution', 'ministryDepartment'])->orderBy('code')->get();
    }

    private function scopeToInstitution($query): void
    {
        $user = auth()->user();
        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $query->whereHas('dataset', fn ($q) => $q->where('department_id', $user->department_id));
        }
    }

    private function authorizeView(Submission $submission): void
    {
        $this->authorize('submissions.view');
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $submission->institution_id !== $user->institution_id, 403);
        abort_if($user->isMinistryDepartmentUser()
            && $submission->dataset?->department_id !== $user->department_id, 403);
    }

    private function authorizeEdit(Submission $submission): void
    {
        $this->authorize('submissions.edit-own');
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $submission->institution_id !== $user->institution_id, 403);
        abort_if($user->isMinistryDepartmentUser()
            && $submission->dataset?->department_id !== $user->department_id, 403);
        abort_unless($submission->isEditable(), 403, 'Only draft or returned submissions can be edited.');
    }

    private function authorizeDataset(Dataset $dataset): void
    {
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $dataset->institution_id !== $user->institution_id, 403);
        abort_if($user->isMinistryDepartmentUser() && $dataset->department_id !== $user->department_id, 403);
        abort_unless($dataset->is_active, 403, 'This dataset is not active.');
    }

    /** JSON feed for the AJAX submissions table (DataTables). */
    public function datatable(Request $request)
    {
        $this->authorize('submissions.view');

        $query = Submission::with(['institution', 'dataset.ministryDepartment', 'submitter'])->latest();
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
        if ($dataset = $request->query('dataset')) {
            $query->whereHas('dataset', fn ($q) => $q->where('code', $dataset));
        }

        $rows = $query->get()->map(fn (Submission $s) => [
            'reference' => '<a href="'.route('submissions.show', $s).'" class="text-decoration-none"><code>'.e($s->reference).'</code></a>'
                .($s->batch_reference ? '<div class="small text-muted"><i class="bi bi-collection me-1"></i>'.e($s->batch_reference).'</div>' : ''),
            'institution' => e($s->institution->code ?? ($s->dataset?->ministryDepartment ? 'MIT/'.$s->dataset->ministryDepartment->code : '—')),
            'dataset' => e($s->dataset->code ?? '—'),
            'period' => e($s->reporting_period),
            'channel' => '<span class="badge text-bg-'.($s->channel === 'api' ? 'info' : 'secondary').'">'.strtoupper(e($s->channel)).'</span>',
            'status' => '<span class="badge text-bg-'.$s->statusBadge().'">'.e($s->statusLabel()).'</span>',
            'records' => (int) $s->records_count,
            'submitter' => e($s->submitter->name ?? '—'),
            'created' => $s->created_at->format('d M Y H:i'),
        ]);

        return response()->json(['data' => $rows]);
    }

}
