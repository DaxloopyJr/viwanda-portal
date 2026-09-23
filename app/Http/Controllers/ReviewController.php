<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Submission;
use Illuminate\Http\Request;

/**
 * Ministry approval chain — mirrors the institutional one:
 *   ministry reviewer (supervisor) -> final approver
 * The reviewer takes submissions under review, recommends them to the final
 * approver, or returns/rejects them. The final approver accepts (then publishes)
 * or returns/rejects. Both can return for rectification, and every action applies
 * to the whole batch.
 */
class ReviewController extends Controller
{
    /** Reviewer picks up a submitted batch. */
    public function startReview(Submission $submission)
    {
        $this->authorize('submissions.review');
        $targets = $this->targets($submission, ['submitted'], 'Only submitted entries can be taken under review.');

        foreach ($targets as $target) {
            $this->transition($target, 'under_review', 'review.started');
        }

        return back()->with('success', $this->message($submission, $targets, 'now under review'));
    }

    /** Reviewer recommends the batch to the final approver. */
    public function recommend(Submission $submission)
    {
        $this->authorize('submissions.recommend');
        $targets = $this->targets($submission, ['under_review'], 'Only submissions under review can be recommended for approval.');

        foreach ($targets as $target) {
            $target->update(['reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'review_comments' => null]);
            $this->transition($target, 'pending_approval', 'review.recommended');
        }

        return back()->with('success', $this->message($submission, $targets, 'recommended to the final approver'));
    }

    /** Final approver accepts the batch (ministry managers may also accept directly). */
    public function accept(Submission $submission)
    {
        $this->authorize('submissions.accept');
        $targets = $this->targets($submission, ['submitted', 'under_review', 'pending_approval'],
            'Only submissions awaiting ministry approval can be accepted.');

        foreach ($targets as $target) {
            $target->update(['reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'review_comments' => null]);
            $this->transition($target, 'accepted', 'review.accepted');
        }

        return back()->with('success', $this->message($submission, $targets, 'accepted'));
    }

    /** Reviewer or final approver returns the batch for rectification. */
    public function sendBack(Request $request, Submission $submission)
    {
        $this->authorize('submissions.return');
        $targets = $this->targets($submission, ['submitted', 'under_review', 'pending_approval'],
            'Only submissions awaiting ministry approval can be returned.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        foreach ($targets as $target) {
            $target->update([
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_comments' => $request->input('review_comments'),
            ]);
            $this->transition($target, 'returned', 'review.returned');
        }

        return back()->with('success', $this->message($submission, $targets, 'returned for correction'));
    }

    public function reject(Request $request, Submission $submission)
    {
        $this->authorize('submissions.reject');
        $targets = $this->targets($submission, ['submitted', 'under_review', 'pending_approval'],
            'Only submissions awaiting ministry approval can be rejected.');
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        foreach ($targets as $target) {
            $target->update([
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_comments' => $request->input('review_comments'),
            ]);
            $this->transition($target, 'rejected', 'review.rejected');
        }

        return back()->with('success', $this->message($submission, $targets, 'rejected'));
    }

    public function publish(Submission $submission)
    {
        $this->authorize('submissions.publish');
        $targets = $this->targets($submission, ['accepted'], 'Only accepted submissions can be published.');

        foreach ($targets as $target) {
            $target->update(['published_at' => now()]);
            $this->transition($target, 'published', 'review.published');
        }

        return back()->with('success', $this->message($submission, $targets,
            'published to the central repository — now available to dashboards and reports'));
    }

    private function targets(Submission $submission, array $statuses, string $error)
    {
        abort_unless(in_array($submission->status, $statuses, true), 422, $error);

        return $submission->batchSiblings()->filter(
            fn (Submission $s) => in_array($s->status, $statuses, true)
        )->values();
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
