<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Submission;
use App\Support\ReportRegistry;
use App\Support\ThematicReportEngine;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Thematic management, operational and public reports (R1–R14) proposed in
 * "Proposed Management and Public Reports for National Growth Decision-Making"
 * derived from the FCT and TBS data parameters.
 */
class ThematicReportController extends Controller
{
    public function __construct(private ThematicReportEngine $engine) {}

    /** Report catalogue grouped by tier. */
    public function index()
    {
        $this->authorize('reports.view');

        return view('reports.thematic.index', [
            'reports' => ReportRegistry::all(),
            'tiers' => ReportRegistry::TIERS,
        ]);
    }

    /** A single thematic report with computed indicators. */
    public function show(Request $request, string $key)
    {
        $this->authorize('reports.view');

        $def = ReportRegistry::find($key);
        abort_unless($def, 404);

        $filters = $this->filters($request, $def);
        $sections = $this->engine->compute($def, $filters, $request->user());

        return view('reports.thematic.show', [
            'def' => $def,
            'tier' => ReportRegistry::TIERS[$def['tier']],
            'sections' => $sections,
            'filters' => $filters,
            'periods' => $this->periodOptions($def),
            'institutions' => Institution::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    /** CSV export of the report's computed sections. */
    public function export(Request $request, string $key): StreamedResponse
    {
        $this->authorize('reports.export');

        $def = ReportRegistry::find($key);
        abort_unless($def, 404);

        $filters = $this->filters($request, $def);
        $sections = $this->engine->compute($def, $filters, $request->user());
        $filename = 'viwanda-'.strtolower($def['key']).'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($def, $sections, $filters) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [$def['key'].' — '.$def['title']]);
            fputcsv($out, ['Period filter: '.($filters['period'] === 'all' ? 'All periods' : $filters['period']).
                ' | Institution filter: '.($filters['institution'] === 'all' ? 'All' : $filters['institution']).
                ' | Generated: '.now()->format('d M Y H:i')]);
            fputcsv($out, []);

            foreach ($sections as $section) {
                fputcsv($out, [$section['title'] ?? $section['type']]);
                match ($section['type']) {
                    'kpis' => $this->csvKpis($out, $section),
                    'breakdown' => $this->csvBreakdown($out, $section),
                    'trend' => $this->csvTrend($out, $section),
                    'submission_compliance' => $this->csvCompliance($out, $section),
                    default => null,
                };
                fputcsv($out, []);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ------------------------------------------------------------ CSV writers

    private function csvKpis($out, array $section): void
    {
        fputcsv($out, ['Indicator', 'Value', 'Basis']);
        foreach ($section['items'] as $item) {
            fputcsv($out, [$item['label'], $item['display'], $item['basis'] ?? '']);
        }
    }

    private function csvBreakdown($out, array $section): void
    {
        fputcsv($out, ['Category', 'Records', 'Share %']);
        foreach ($section['rows'] as $row) {
            $share = ($section['total'] ?? 0) > 0 ? round($row['count'] / $section['total'] * 100, 1) : 0;
            fputcsv($out, [$row['label'], $row['count'], $share]);
        }
    }

    private function csvTrend($out, array $section): void
    {
        fputcsv($out, ['Period', 'Value']);
        foreach ($section['points'] as $point) {
            fputcsv($out, [$point['period'], $point['value']]);
        }
    }

    private function csvCompliance($out, array $section): void
    {
        fputcsv($out, ['Owner', 'Expected datasets', 'Received', 'Coverage %', 'Approved', 'In pipeline', 'Returned/Rejected', 'First-pass %', 'Returns for rectification', 'On-time %']);
        foreach ($section['rows'] as $row) {
            fputcsv($out, [
                $row['owner'], $row['expected'], $row['received'], $row['coverage'],
                $row['approved'], $row['pipeline'], $row['returned'],
                $row['first_pass'], $row['returns'], $row['timeliness'],
            ]);
        }
    }

    // ---------------------------------------------------------------- helpers

    private function filters(Request $request, array $def): array
    {
        $user = $request->user();
        return [
            'period' => $request->query('period', 'all'),
            'institution' => $user->isInstitutionUser() ? 'own' : $request->query('institution', 'all'),
        ];
    }

    /**
     * Period options: distinct submission periods plus the distinct years
     * (annual roll-up, used prominently by R12).
     */
    private function periodOptions(array $def): array
    {
        $periods = Submission::select('reporting_period')->distinct()
            ->pluck('reporting_period')
            ->sortBy(fn ($p) => $this->engine->periodKey($p))
            ->values();

        $years = $periods->map(fn ($p) => preg_match('/(\d{4})/', $p, $m) ? $m[1] : null)
            ->filter()->unique()->sort()->values();

        return ['periods' => $periods, 'years' => $years];
    }
}
