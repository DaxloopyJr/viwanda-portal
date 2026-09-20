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
        }

        $stats = [
            'institutions' => Institution::where('is_active', true)->count(),
            'submissions' => (clone $base)->count(),
            'pending_review' => (clone $base)->whereIn('status', ['submitted', 'under_review'])->count(),
            'internal_queue' => (clone $base)->whereIn('status', ['internal_review', 'accounting_review'])->count(),
            'published' => (clone $base)->where('status', 'published')->count(),
        ];
        $stats['acceptance_rate'] = $this->acceptanceRate(clone $base);

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
            ->join('institutions', 'submissions.institution_id', '=', 'institutions.id')
            ->select('institutions.code', DB::raw('count(*) as total'))
            ->groupBy('institutions.code')->orderByDesc('total')->limit(13)->get();

        $recent = (clone $base)->with(['institution', 'dataset', 'submitter'])
            ->latest()->limit(8)->get();

        // Compliance: active institutions vs submissions this quarter
        $period = now()->format('Y').'-Q'.ceil(now()->month / 3);
        $compliance = Institution::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('id', $user->institution_id))
            ->withCount(['submissions as period_submissions' => fn ($q) => $q->where('reporting_period', $period)])
            ->orderBy('code')->get();

        return view('dashboard', compact('stats', 'monthlyLabels', 'monthlyData', 'statusCounts', 'statusOrder', 'perInstitution', 'recent', 'compliance', 'period'));
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
