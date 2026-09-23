<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Submission;
use Illuminate\Http\Request;

/**
 * Internal institutional approval chain:
 *   officer -> supervisor -> accounting officer -> Ministry (viwanda)
 *
 * Every action applies to the whole submission batch: all batch members whose
 * current status matches the action's precondition move together.
 */
class InternalReviewController extends Controller
{
    /** Supervisor forwards a submission to the institutional accounting officer. */
    public function forward(Submission $submission)
    {
        $this->authorize('submissions.review-internal');
        $this->guardInstitution($submission);
        $targets = $this->targets($submission, ['internal_review', 'returned_supervisor'],
            'Only submissions awaiting internal review can be forwarded.');

        foreach ($targets as $target) {
            $target->update(['review_comments' => null]);
            $this->transition($target, 'accounting_review', 'internal.forwarded');
        }

        return back()->with('success', $this->message($submission, $targets, 'forwarded to the institutional accounting officer'));
    }

    /** Supervisor returns a submission to the data officer for rectification. */
    public function returnToOfficer(Request $request, Submission $submission)
    {
        $this->authorize('submissions.review-internal');
        $this->guardInstitution($submission);
        $targets = $this->targets($submission, ['internal_review', 'returned_supervisor'],
            'Only submissions awaiting internal review can be returned.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        foreach ($targets as $target) {
            $target->update(['review_comments' => $request->input('review_comments')]);
            $this->transition($target, 'returned_officer', 'internal.returned_officer');
        }

        return back()->with('success', $this->message($submission, $targets, 'returned to the data officer for rectification'));
    }

    /** Accounting officer approves and submits the data/report to the Ministry (viwanda). */
    public function approve(Submission $submission)
    {
        $this->authorize('submissions.approve-internal');
        $this->guardInstitution($submission);
        $targets = $this->targets($submission, ['accounting_review'],
            'Only submissions awaiting accounting approval can be approved.');

        foreach ($targets as $target) {
            $target->update(['review_comments' => null, 'submitted_at' => now()]);
            $this->transition($target, 'submitted', 'internal.approved');
        }

        return back()->with('success', $this->message($submission, $targets, 'approved and sent to the Ministry for review'));
    }

    /** Accounting officer returns a submission to the supervisor. */
    public function returnToSupervisor(Request $request, Submission $submission)
    {
        $this->authorize('submissions.approve-internal');
        $this->guardInstitution($submission);
        $targets = $this->targets($submission, ['accounting_review'],
            'Only submissions awaiting accounting approval can be returned.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        foreach ($targets as $target) {
            $target->update(['review_comments' => $request->input('review_comments')]);
            $this->transition($target, 'returned_supervisor', 'internal.returned_supervisor');
        }

        return back()->with('success', $this->message($submission, $targets, 'returned to the supervisor'));
    }

    /**
     * The batch members eligible for this action: all siblings already in one of
     * the allowed statuses. The clicked submission itself must be eligible.
     */
    private function targets(Submission $submission, array $statuses, string $error)
    {
        abort_unless(in_array($submission->status, $statuses, true), 422, $error);

        return $submission->batchSiblings()->filter(
            fn (Submission $s) => in_array($s->status, $statuses, true)
        )->values();
    }

    private function guardInstitution(Submission $submission): void
    {
        abort_unless($submission->institution_id === auth()->user()->institution_id, 403);
    }

    private function transition(Submission $submission, string $to, string $action): void
    {
        $old = $submission->status;
        $submission->update(['status' => $to]);
        AuditLog::record($action, $submission, ['status' => $old], ['status' => $to]);
    }

    private function message(Submission $submission, $targets, string $what): string
    {
        $count = $targets->count();
        if ($submission->batch_reference && $count > 1) {
            return "Batch {$submission->batch_reference}: {$count} submissions {$what}.";
        }

        return "Submission {$submission->reference} {$what}.";
    }
}
