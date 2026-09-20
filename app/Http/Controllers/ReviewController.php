<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Submission;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function startReview(Submission $submission)
    {
        $this->authorize('submissions.review');
        abort_unless($submission->status === 'submitted', 422, 'Only submitted entries can be taken under review.');

        $this->transition($submission, 'under_review', 'review.started');

        return back()->with('success', 'Submission is now under review.');
    }

    public function accept(Submission $submission)
    {
        $this->authorize('submissions.accept');
        abort_unless(in_array($submission->status, ['submitted', 'under_review'], true), 422);

        $submission->update(['reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'review_comments' => null]);
        $this->transition($submission, 'accepted', 'review.accepted');

        return back()->with('success', 'Submission accepted.');
    }

    public function sendBack(Request $request, Submission $submission)
    {
        $this->authorize('submissions.return');
        abort_unless(in_array($submission->status, ['submitted', 'under_review'], true), 422);
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        $submission->update([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_comments' => $request->input('review_comments'),
        ]);
        $this->transition($submission, 'returned', 'review.returned');

        return back()->with('success', 'Submission returned to the institution for correction.');
    }

    public function reject(Request $request, Submission $submission)
    {
        $this->authorize('submissions.reject');
        abort_unless(in_array($submission->status, ['submitted', 'under_review'], true), 422);
        $request->validate(['review_comments' => ['required', 'string', 'max:2000']]);

        $submission->update([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_comments' => $request->input('review_comments'),
        ]);
        $this->transition($submission, 'rejected', 'review.rejected');

        return back()->with('success', 'Submission rejected.');
    }

    public function publish(Submission $submission)
    {
        $this->authorize('submissions.publish');
        abort_unless($submission->status === 'accepted', 422, 'Only accepted submissions can be published.');

        $submission->update(['published_at' => now()]);
        $this->transition($submission, 'published', 'review.published');

        return back()->with('success', 'Submission published to the central repository. It is now available to dashboards and reports.');
    }

    private function transition(Submission $submission, string $to, string $action): void
    {
        $old = $submission->status;
        $submission->update(['status' => $to]);
        AuditLog::record($action, $submission, ['status' => $old], ['status' => $to]);
    }
}
