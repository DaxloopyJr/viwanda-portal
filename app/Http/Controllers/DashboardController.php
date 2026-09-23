<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $this->authorize('dashboard.view');

        $user = auth()->user();
        $base = Submission::query();
        if ($user->isInstitutionUser()) {
            $base->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $base->whereHas('dataset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        $stats = [
            'institutions' => Institution::where('is_active', true)->count(),
            'datasets' => \App\Models\Dataset::where('is_active', true)->count(),
            'submissions' => (clone $base)->count(),
            'pending_review' => (clone $base)->whereIn('status', ['submitted', 'under_review', 'pending_approval'])->count(),
            'internal_queue' => (clone $base)->whereIn('status', ['internal_review', 'accounting_review'])->count(),
            'published' => (clone $base)->where('status', 'published')->count(),
            'returned' => (clone $base)->whereIn('status', ['returned', 'returned_officer', 'returned_supervisor', 'rejected'])->count(),
        ];
        $stats['acceptance_rate'] = $this->acceptanceRate(clone $base);

        // Detail rows behind each clickable stat card
        $cardDetails = [
            'institutions' => Institution::where('is_active', true)
                ->withCount(['datasets', 'submissions'])
                ->orderBy('code')->get()
                ->map(fn ($i) => ['label' => $i->code.' — '.$i->name, 'meta' => $i->datasets_count.' datasets', 'value' => $i->submissions_count.' submissions']),
            'datasets' => \App\Models\Dataset::where('is_active', true)
                ->when($user->isInstitutionUser(), fn ($q) => $q->where('institution_id', $user->institution_id))
                ->when($user->isMinistryDepartmentUser(), fn ($q) => $q->where('department_id', $user->department_id))
                ->with(['institution', 'ministryDepartment'])->orderBy('code')->get()
                ->map(fn ($d) => ['label' => $d->code.' — '.$d->name, 'meta' => $d->ownerLabel(), 'value' => ucfirst($d->frequency)]),
            'submissions' => (clone $base)->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->dataset->code, 'value' => $s->statusLabel(), 'url' => route('submissions.show', $s)]),
            'pending_review' => (clone $base)->whereIn('status', ['submitted', 'under_review', 'pending_approval'])
                ->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->reporting_period, 'value' => $s->statusLabel(), 'url' => route('submissions.show', $s)]),
            'internal_queue' => (clone $base)->whereIn('status', ['internal_review', 'accounting_review'])
                ->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->reporting_period, 'value' => $s->statusLabel(), 'url' => route('submissions.show', $s)]),
            'published' => (clone $base)->where('status', 'published')
                ->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->reporting_period, 'value' => $s->published_at?->format('d M Y') ?? '', 'url' => route('submissions.show', $s)]),
            'returned' => (clone $base)->whereIn('status', ['returned', 'returned_officer', 'returned_supervisor', 'rejected'])
                ->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->reporting_period, 'value' => $s->statusLabel(), 'url' => route('submissions.show', $s)]),
            'acceptance_rate' => (clone $base)->whereIn('status', ['accepted', 'rejected', 'published'])
                ->with(['institution', 'dataset'])->latest()->limit(12)->get()
                ->map(fn ($s) => ['label' => $s->reference, 'meta' => ($s->institution->code ?? 'MIT').' · '.$s->reporting_period, 'value' => $s->statusLabel(), 'url' => route('submissions.show', $s)]),
        ];

        // Line/bar chart: submissions per month (last 12 months)
        $driver = DB::connection()->getDriverName();
        $monthExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };
        $monthly = (clone $base)
            ->selectRaw("$monthExpr as month, count(*) as total")
            ->groupBy('month')->orderBy('month')->pluck('total', 'month');

        $months = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $monthlyLabels = $months->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'));
        $monthlyData = $months->map(fn ($m) => (int) ($monthly[$m] ?? 0));

        // Pie chart: status distribution
        $statusCounts = (clone $base)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');
        $statusOrder = \App\Models\Submission::STATUSES;

        // Bar chart: submissions per institution
        $perInstitution = (clone $base)
            ->leftJoin('institutions', 'submissions.institution_id', '=', 'institutions.id')
            ->selectRaw("COALESCE(institutions.code, 'MIT') as code, count(*) as total")
            ->groupBy('code')->orderByDesc('total')->limit(13)->get();

        $recent = (clone $base)->with(['institution', 'dataset', 'submitter'])
            ->latest()->limit(8)->get();

        // Compliance: active institutions vs submissions this quarter
        $period = sprintf('%s - Q%d', now()->format('Y'), (int) ceil(now()->month / 3));
        $compliance = Institution::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('id', $user->institution_id))
            ->withCount(['submissions as period_submissions' => fn ($q) => $q->where('reporting_period', $period)])
            ->orderBy('code')->get();

        return view('dashboard', compact('stats', 'cardDetails', 'monthlyLabels', 'monthlyData', 'statusCounts', 'statusOrder', 'perInstitution', 'recent', 'compliance', 'period'));
    }

    private function acceptanceRate($query): ?float
    {
        $decided = (clone $query)->whereIn('status', ['accepted', 'rejected', 'published'])->count();
        if ($decided === 0) {
            return null;
        }
        $accepted = (clone $query)->whereIn('status', ['accepted', 'published'])->count();

        return round($accepted / $decided * 100, 1);
    }
}
