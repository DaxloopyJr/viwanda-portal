<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Submission;
use Illuminate\Http\Request;

/**
 * Internal institutional approval chain:
 *   officer -> supervisor -> accounting officer -> Ministry (viwanda)
 */
class InternalReviewController extends Controller
{
    /** Supervisor forwards a submission to the institutional accounting officer. */
    public function forward(Submission $submission)
    {
        $this->authorize('submissions.review-internal');
        $this->guardInstitution($submission);
        abort_unless(in_array($submission->status, ['internal_review', 'returned_supervisor'], true),
            422, 'Only submissions awaiting internal review can be forwarded.');

        $submission->update(['review_comments' => null]);
        $this->transition($submission, 'accounting_review', 'internal.forwarded');

        return back()->with('success', 'Submission forwarded to the institutional accounting officer.');
    }

    /** Supervisor returns a submission to the data officer for rectification. */
    public function returnToOfficer(Request $request, Submission $submission)
    {
        $this->authorize('submissions.review-internal');
        $this->guardInstitution($submission);
        abort_unless(in_array($submission->status, ['internal_review', 'returned_supervisor'], true),
            422, 'Only submissions awaiting internal review can be returned.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        $submission->update(['review_comments' => $request->input('review_comments')]);
        $this->transition($submission, 'returned_officer', 'internal.returned_officer');

        return back()->with('success', 'Submission returned to the data officer for rectification.');
    }

    /** Accounting officer approves and submits the data/report to the Ministry (viwanda). */
    public function approve(Submission $submission)
    {
        $this->authorize('submissions.approve-internal');
        $this->guardInstitution($submission);
        abort_unless($submission->status === 'accounting_review',
            422, 'Only submissions awaiting accounting approval can be approved.');

        $submission->update([
            'review_comments' => null,
            'submitted_at' => now(),
        ]);
        $this->transition($submission, 'submitted', 'internal.approved');

        return back()->with('success', "Submission {$submission->reference} approved and sent to the Ministry for review.");
    }

    /** Accounting officer returns a submission to the supervisor. */
    public function returnToSupervisor(Request $request, Submission $submission)
    {
        $this->authorize('submissions.approve-internal');
        $this->guardInstitution($submission);
        abort_unless($submission->status === 'accounting_review',
            422, 'Only submissions awaiting accounting approval can be returned.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        $submission->update(['review_comments' => $request->input('review_comments')]);
        $this->transition($submission, 'returned_supervisor', 'internal.returned_supervisor');

        return back()->with('success', 'Submission returned to the supervisor.');
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
}
