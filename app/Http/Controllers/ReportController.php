<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function consolidated()
    {
        $this->authorize('reports.view');

        $user = auth()->user();
        $byInstitution = Submission::select(
                'institutions.code', 'institutions.name',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status = 'published' then 1 else 0 end) as published"),
                DB::raw("sum(case when status in ('accepted') then 1 else 0 end) as accepted"),
                DB::raw("sum(case when status in ('submitted','under_review') then 1 else 0 end) as pending"),
                DB::raw("sum(case when status in ('returned','rejected') then 1 else 0 end) as rejected_returned")
            )
            ->join('institutions', 'submissions.institution_id', '=', 'institutions.id')
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('submissions.institution_id', $user->institution_id))
            ->groupBy('institutions.code', 'institutions.name')
            ->orderBy('institutions.code')->get();

        $byPeriod = Submission::select('reporting_period', DB::raw('count(*) as total'))
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('institution_id', $user->institution_id))
            ->groupBy('reporting_period')->orderByDesc('reporting_period')->get();

        return view('reports.consolidated', compact('byInstitution', 'byPeriod'));
    }

    public function compliance()
    {
        $this->authorize('reports.view');

        $periods = Submission::select('reporting_period')->distinct()->orderByDesc('reporting_period')->pluck('reporting_period');
        $period = request('period', $periods->first() ?? now()->format('Y').'-Q'.ceil(now()->month / 3));

        $institutions = Institution::where('is_active', true)
            ->when(auth()->user()->isInstitutionUser(), fn ($q) => $q->where('id', auth()->user()->institution_id))
            ->with(['datasets' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('code')->get();

        $rows = [];
        foreach ($institutions as $institution) {
            foreach ($institution->datasets as $dataset) {
                $submission = Submission::where('dataset_id', $dataset->id)->where('reporting_period', $period)->latest()->first();
                $rows[] = [
                    'institution' => $institution,
                    'dataset' => $dataset,
                    'submission' => $submission,
                    'compliant' => $submission && in_array($submission->status, ['internal_review', 'accounting_review', 'submitted', 'under_review', 'accepted', 'published'], true),
                ];
            }
        }

        return view('reports.compliance', compact('rows', 'periods', 'period'));
    }

    public function export(): StreamedResponse
    {
        $this->authorize('reports.export');

        $submissions = Submission::with(['institution', 'dataset', 'submitter', 'reviewer'])
            ->when(auth()->user()->isInstitutionUser(), fn ($q) => $q->where('institution_id', auth()->user()->institution_id))
            ->latest()->get();

        return response()->streamDownload(function () use ($submissions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Institution', 'Dataset', 'Period', 'Channel', 'Status', 'Records', 'Submitted By', 'Submitted At', 'Reviewed By', 'Reviewed At', 'Published At']);
            foreach ($submissions as $s) {
                fputcsv($out, [
                    $s->reference, $s->institution->code, $s->dataset->code, $s->reporting_period,
                    $s->channel, $s->status, $s->records_count,
                    $s->submitter?->name, $s->submitted_at, $s->reviewer?->name, $s->reviewed_at, $s->published_at,
                ]);
            }
            fclose($out);
        }, 'viwanda-submissions-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
