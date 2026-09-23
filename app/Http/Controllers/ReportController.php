<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\Institution;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const DECIDED = ['accepted', 'rejected', 'published'];
    private const PIPELINE = ['internal_review', 'accounting_review', 'submitted', 'under_review', 'pending_approval'];
    private const RETURNED = ['returned', 'returned_officer', 'returned_supervisor', 'rejected'];

    /**
     * Consolidated statistical report — "Reliable Industry & Trade Data for a
     * Growing Tanzania". Search parameters: institution (or all), reporting
     * period, dataset, status.
     */
    public function consolidated(Request $request)
    {
        $this->authorize('reports.view');

        $filters = $this->filters($request);

        // Summary statistics
        $base = $this->scopedQuery($filters);
        $summary = [
            'submissions' => (clone $base)->count(),
            'records' => (int) ((clone $base)->sum('records_count') ?? 0),
            'published' => (clone $base)->where('status', 'published')->count(),
            'acceptance_rate' => $this->acceptanceRate(clone $base),
            'datasets_covered' => (clone $base)->distinct()->count('dataset_id'),
            'reporters' => (clone $base)->whereIn('status', array_merge(self::PIPELINE, self::DECIDED))
                ->distinct()->count('institution_id'),
        ];

        // Dataset coverage for the selected scope: which catalogue datasets have
        // been reported in the selected period, and their latest status.
        $coverageQuery = Dataset::where('is_active', true)
            ->with(['institution', 'ministryDepartment'])->orderBy('code');
        $this->scopeDatasets($coverageQuery, $filters);
        $coverage = $coverageQuery->get()->map(function (Dataset $d) use ($filters) {
            $subs = Submission::where('dataset_id', $d->id)
                ->when($filters['period'] !== 'all', fn ($q) => $q->where('reporting_period', $filters['period']))
                ->latest()->get();

            return [
                'dataset' => $d,
                'count' => $subs->count(),
                'latest' => $subs->first(),
            ];
        });

        return view('reports.consolidated', [
            'filters' => $filters,
            'summary' => $summary,
            'coverage' => $coverage,
            'institutions' => Institution::where('is_active', true)->orderBy('code')->get(),
            'periods' => Submission::select('reporting_period')->distinct()->orderByDesc('reporting_period')->pluck('reporting_period'),
            'datasets' => Dataset::where('is_active', true)->orderBy('code')->get(['id', 'code', 'name']),
            'statuses' => Submission::STATUSES,
        ]);
    }

    public function compliance()
    {
        $this->authorize('reports.view');

        $periods = Submission::select('reporting_period')->distinct()->orderByDesc('reporting_period')->pluck('reporting_period');
        $period = request('period', $periods->first() ?? sprintf('%s - Q%d', now()->format('Y'), (int) ceil(now()->month / 3)));

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
                    'compliant' => $submission && in_array($submission->status, array_merge(self::PIPELINE, self::DECIDED), true),
                ];
            }
        }

        return view('reports.compliance', compact('rows', 'periods', 'period'));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('reports.export');

        $filters = $this->filters($request);
        $submissions = $this->scopedQuery($filters)
            ->with(['institution', 'dataset.ministryDepartment', 'submitter', 'reviewer'])
            ->latest()->get();

        return response()->streamDownload(function () use ($submissions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Batch', 'Institution/Department', 'Dataset', 'Period', 'Consumers', 'Channel', 'Status', 'Records', 'Submitted By', 'Submitted At', 'Reviewed By', 'Reviewed At', 'Published At']);
            foreach ($submissions as $s) {
                fputcsv($out, [
                    $s->reference, $s->batch_reference,
                    $s->institution->code ?? ($s->dataset?->ministryDepartment?->code ?? 'MIT'),
                    $s->dataset->code, $s->reporting_period,
                    implode(', ', $s->consumers ?? []),
                    $s->channel, $s->status, $s->records_count,
                    $s->submitter?->name, $s->submitted_at, $s->reviewer?->name, $s->reviewed_at, $s->published_at,
                ]);
            }
            fclose($out);
        }, 'viwanda-submissions-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    /** JSON feed: institution-wise statistical summary (DataTables + charts). */
    public function consolidatedData(Request $request)
    {
        $this->authorize('reports.view');
        $filters = $this->filters($request);

        $rows = $this->scopedQuery($filters)
            ->leftJoin('institutions', 'submissions.institution_id', '=', 'institutions.id')
            ->selectRaw("COALESCE(institutions.code, 'MIT') as code")
            ->selectRaw("COALESCE(institutions.name, 'Ministry Departments') as name")
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(records_count) as records')
            ->selectRaw("sum(case when status = 'published' then 1 else 0 end) as published")
            ->selectRaw("sum(case when status in ('accepted') then 1 else 0 end) as accepted")
            ->selectRaw("sum(case when status in ('internal_review','accounting_review','submitted','under_review','pending_approval') then 1 else 0 end) as pending")
            ->selectRaw("sum(case when status in ('returned','rejected','returned_officer','returned_supervisor') then 1 else 0 end) as rejected_returned")
            ->groupBy('code', 'name')
            ->orderBy('code')->get()
            ->map(fn ($r) => [
                'code' => '<code>'.e($r->code).'</code>',
                'name' => e($r->name),
                'total' => (int) $r->total,
                'records' => (int) $r->records,
                'published' => '<span class="badge text-bg-primary">'.$r->published.'</span>',
                'accepted' => '<span class="badge text-bg-success">'.$r->accepted.'</span>',
                'pending' => '<span class="badge text-bg-warning">'.$r->pending.'</span>',
                'rejected_returned' => '<span class="badge text-bg-danger">'.$r->rejected_returned.'</span>',
            ]);

        return response()->json(['data' => $rows]);
    }

    /** JSON feed: detailed submission-level report rows. */
    public function consolidatedDetail(Request $request)
    {
        $this->authorize('reports.view');
        $filters = $this->filters($request);

        $rows = $this->scopedQuery($filters)
            ->with(['institution', 'dataset.ministryDepartment', 'submitter'])
            ->latest()->get()
            ->map(fn (Submission $s) => [
                'reference' => '<a href="'.route('submissions.show', $s).'" class="text-decoration-none"><code>'.e($s->reference).'</code></a>'
                    .($s->batch_reference ? '<div class="small text-muted">'.e($s->batch_reference).'</div>' : ''),
                'owner' => e($s->institution->code ?? ('MIT/'.($s->dataset?->ministryDepartment?->code ?? ''))),
                'dataset' => e($s->dataset->code ?? '—').' <span class="text-muted small">'.e(\Illuminate\Support\Str::limit($s->dataset->name ?? '', 40)).'</span>',
                'period' => e($s->reporting_period),
                'consumers' => collect($s->consumers ?? [])->map(fn ($c) => '<span class="badge text-bg-light border me-1">'.e($c).'</span>')->implode('') ?: '<span class="text-muted">—</span>',
                'records' => (int) $s->records_count,
                'status' => '<span class="badge text-bg-'.$s->statusBadge().'">'.e($s->statusLabel()).'</span>',
                'submitted' => '<span class="small">'.e($s->submitter->name ?? '—').'<br><span class="text-muted">'.($s->submitted_at?->format('d M Y') ?? '—').'</span></span>',
            ]);

        return response()->json(['data' => $rows]);
    }

    /** JSON feed for the AJAX compliance report table (DataTables). */
    public function complianceData()
    {
        $this->authorize('reports.view');

        $period = request('period', sprintf('%s - Q%d', now()->format('Y'), (int) ceil(now()->month / 3)));

        $institutions = Institution::where('is_active', true)
            ->when(auth()->user()->isInstitutionUser(), fn ($q) => $q->where('id', auth()->user()->institution_id))
            ->with(['datasets' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('code')->get();

        $rows = [];
        foreach ($institutions as $institution) {
            foreach ($institution->datasets as $dataset) {
                $submission = Submission::where('dataset_id', $dataset->id)->where('reporting_period', $period)->latest()->first();
                $compliant = $submission && in_array($submission->status, array_merge(self::PIPELINE, self::DECIDED), true);
                $rows[] = [
                    'institution' => '<code>'.e($institution->code).'</code> <span class="text-muted small">'.e($institution->name).'</span>',
                    'dataset' => e($dataset->name).' <span class="text-muted small">('.e($dataset->code).')</span>',
                    'frequency' => ucfirst($dataset->frequency),
                    'submission' => $submission
                        ? '<a href="'.route('submissions.show', $submission).'" class="text-decoration-none"><code>'.e($submission->reference).'</code></a>'
                        : '<span class="text-muted">—</span>',
                    'status' => $submission
                        ? '<span class="badge text-bg-'.$submission->statusBadge().'">'.e($submission->statusLabel()).'</span>'
                        : '<span class="badge text-bg-secondary">No submission</span>',
                    'compliance' => $compliant
                        ? '<span class="badge text-bg-success"><i class="bi bi-check-lg me-1"></i>Compliant</span>'
                        : '<span class="badge text-bg-danger"><i class="bi bi-x-lg me-1"></i>Non-compliant</span>',
                ];
            }
        }

        return response()->json(['data' => $rows]);
    }

    /** Normalized search parameters. */
    private function filters(Request $request): array
    {
        return [
            'institution' => $request->query('institution', 'all'),
            'period' => $request->query('period', 'all'),
            'status' => $request->query('status', 'all'),
            'dataset' => $request->query('dataset', 'all'),
        ];
    }

    /** Submissions query scoped to the user and the report filters. */
    private function scopedQuery(array $filters)
    {
        $user = auth()->user();
        $query = Submission::query();

        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $query->whereHas('dataset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        if ($filters['institution'] !== 'all' && ! $user->isInstitutionUser()) {
            if ($filters['institution'] === 'MIT') {
                $query->whereNull('institution_id');
            } else {
                $query->whereHas('institution', fn ($q) => $q->where('code', $filters['institution']));
            }
        }
        if ($filters['period'] !== 'all') {
            $query->where('reporting_period', $filters['period']);
        }
        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        if ($filters['dataset'] !== 'all') {
            $query->whereHas('dataset', fn ($q) => $q->where('code', $filters['dataset']));
        }

        return $query;
    }

    /** Catalogue datasets visible within the current scope/filters. */
    private function scopeDatasets($query, array $filters): void
    {
        $user = auth()->user();
        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }
        if ($filters['institution'] !== 'all' && ! $user->isInstitutionUser()) {
            if ($filters['institution'] === 'MIT') {
                $query->whereNull('institution_id');
            } else {
                $query->whereHas('institution', fn ($q) => $q->where('code', $filters['institution']));
            }
        }
        if ($filters['dataset'] !== 'all') {
            $query->where('code', $filters['dataset']);
        }
    }

    private function acceptanceRate($query): ?float
    {
        $decided = (clone $query)->whereIn('status', self::DECIDED)->count();
        if ($decided === 0) {
            return null;
        }
        $accepted = (clone $query)->whereIn('status', ['accepted', 'published'])->count();

        return round($accepted / $decided * 100, 1);
    }
}
