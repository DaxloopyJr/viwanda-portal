<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dataset;
use App\Models\Submission;
use App\Models\SubmissionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * POST /api/v1/submissions
     * Idempotent: the same (institution, transaction_reference) returns the
     * existing submission instead of creating a duplicate (SRS FR-055).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->tokenCan('submit'), 403, 'Token lacks the submit ability.');
        abort_unless($user->institution_id, 403, 'API accounts must be linked to an institution.');

        $data = $request->validate([
            'dataset_code' => ['required', 'string'],
            'reporting_period' => ['required', 'string', 'max:20'],
            'transaction_reference' => ['required', 'string', 'max:64'],
            'records' => ['required', 'array', 'min:1'],
        ]);

        $dataset = Dataset::where('code', $data['dataset_code'])->where('is_active', true)->first();
        abort_unless($dataset, 404, 'Unknown or inactive dataset code.');
        abort_unless($dataset->institution_id === $user->institution_id, 403, 'This dataset does not belong to your institution.');

        // Idempotency check
        $existing = Submission::where('institution_id', $user->institution_id)
            ->where('transaction_reference', $data['transaction_reference'])->first();
        if ($existing) {
            return $this->payload($existing, 200, 'Duplicate transaction reference: returning the existing submission.');
        }

        $validator = Validator::make($request->all(), $dataset->validationRules('records.*.'));

        $submission = DB::transaction(function () use ($data, $dataset, $user, $validator) {
            $submission = Submission::create([
                'reference' => Submission::nextReference(),
                'transaction_reference' => $data['transaction_reference'],
                'institution_id' => $dataset->institution_id,
                'dataset_id' => $dataset->id,
                'reporting_period' => $data['reporting_period'],
                'channel' => 'api',
                'status' => 'draft',
                'submitted_by' => $user->id,
            ]);

            foreach ($data['records'] as $i => $record) {
                SubmissionRecord::create([
                    'submission_id' => $submission->id,
                    'row_number' => $i + 1,
                    'data' => $record,
                ]);
            }
            $submission->update(['records_count' => $submission->records()->count()]);

            return $submission;
        });

        $this->validateRecords($submission);

        if ($submission->fresh()->validation_errors) {
            AuditLog::record('api.submission.invalid', $submission);

            return $this->payload($submission, 422, 'Validation failed. Correct the records and resubmit with the same transaction reference.');
        }

        $submission->update(['status' => 'submitted', 'submitted_at' => now()]);
        AuditLog::record('api.submission.received', $submission);

        return $this->payload($submission->fresh(), 201, 'Submission received and queued for Ministry review.');
    }

    /** GET /api/v1/submissions/{reference} — status query (IF-004). */
    public function show(Request $request, string $reference)
    {
        $user = $request->user();
        $submission = Submission::where('reference', $reference)
            ->where('institution_id', $user->institution_id)->first();
        abort_unless($submission, 404, 'Submission not found.');

        return $this->payload($submission, 200);
    }

    private function validateRecords(Submission $submission): void
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

    private function payload(Submission $submission, int $status, string $message = 'OK')
    {
        return response()->json([
            'message' => $message,
            'reference' => $submission->reference,
            'transaction_reference' => $submission->transaction_reference,
            'status' => $submission->status,
            'records_count' => $submission->records_count,
            'validation_errors' => $submission->validation_errors,
            'submitted_at' => $submission->submitted_at,
            'reviewed_at' => $submission->reviewed_at,
            'review_comments' => $submission->review_comments,
            'published_at' => $submission->published_at,
        ], $status);
    }
}
