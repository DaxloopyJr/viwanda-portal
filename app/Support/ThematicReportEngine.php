<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\Dataset;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes the indicators, breakdowns and trends for the thematic reports
 * (ReportRegistry) from submission records that have completed the approval
 * chain. Tier 1/2 reports use Ministry-approved data (accepted + published);
 * Tier 3 public reports use published data only (implementation note §6).
 */
class ThematicReportEngine
{
    /** Approved statuses feeding each tier. */
    public static function statusesForTier(int $tier): array
    {
        return $tier === 3 ? ['published'] : ['accepted', 'published'];
    }

    /**
     * Compute all sections of a report definition.
     *
     * @param  array  $filters  ['period' => '2026 - Q1'|'2026'|'all', 'institution' => code|'MIT'|'all']
     */
    public function compute(array $def, array $filters, User $user): array
    {
        $sections = [];
        $needsRecords = collect($def['sections'])->contains(fn ($s) => $s['type'] !== 'submission_compliance');

        $records = collect();
        $trendRecords = collect();
        if ($needsRecords) {
            [$records, $trendRecords] = $this->records($def, $filters, $user);
        }

        foreach ($def['sections'] as $section) {
            $sections[] = match ($section['type']) {
                'kpis' => $this->kpis($section, $records),
                'breakdown' => $this->breakdown($section, $records),
                'trend' => $this->trend($section, $trendRecords),
                'submission_compliance' => $this->submissionCompliance($section, $filters, $user),
                default => $section,
            };
        }

        return $sections;
    }

    /**
     * Flatten approved submission records for the report's datasets into rows
     * tagged with dataset code and reporting period. Returns [filtered, trend]
     * record sets — the trend set ignores the period filter so charts keep
     * their time axis.
     */
    private function records(array $def, array $filters, User $user): array
    {
        $datasets = Dataset::whereIn('code', $def['datasets'])->get();
        $codeById = $datasets->pluck('code', 'id');

        $query = Submission::with('records')
            ->whereIn('status', static::statusesForTier($def['tier']))
            ->whereIn('dataset_id', $datasets->pluck('id'));

        $this->scope($query, $filters, $user);

        $trendQuery = (clone $query);
        $this->applyPeriod($query, $filters['period'] ?? 'all');

        $flatten = fn ($submissions) => $submissions->flatMap(function (Submission $s) use ($codeById) {
            $code = $codeById[$s->dataset_id] ?? null;
            return $s->records->map(fn ($r) => ['dataset' => $code, 'period' => $s->reporting_period] + (array) $r->data);
        });

        return [$flatten($query->get()), $flatten($trendQuery->get())];
    }

    /** Apply user + institution scoping to a submissions query. */
    private function scope($query, array $filters, User $user): void
    {
        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $query->whereHas('dataset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        if (($filters['institution'] ?? 'all') !== 'all' && ! $user->isInstitutionUser()) {
            if ($filters['institution'] === 'MIT') {
                $query->whereNull('institution_id');
            } else {
                $query->whereHas('institution', fn ($q) => $q->where('code', $filters['institution']));
            }
        }
    }

    /** Period may be exact ("2026 - Q1"), a year ("2026"), or "all". */
    private function applyPeriod($query, string $period): void
    {
        if ($period === 'all' || $period === '') {
            return;
        }
        if (preg_match('/^\d{4}$/', $period)) {
            $query->where('reporting_period', 'like', $period.'%');
        } else {
            $query->where('reporting_period', $period);
        }
    }

    // ---------------------------------------------------------------- sections

    private function kpis(array $section, Collection $records): array
    {
        $section['items'] = collect($section['items'])->map(function (array $ind) use ($records) {
            $raw = $this->indicator($ind, $records);
            $ind['raw'] = $raw;
            $ind['display'] = $this->format($raw, $ind['format'] ?? 'number');
            return $ind;
        })->all();

        return $section;
    }

    private function breakdown(array $section, Collection $records): array
    {
        $rows = $records->where('dataset', $section['dataset']);
        $counts = $rows->groupBy(fn ($r) => trim((string) ($r[$section['field']] ?? '')) !== '' ? trim((string) $r[$section['field']]) : '—')
            ->map->count()->sortDesc();

        $top = $counts->take(8);
        if ($counts->count() > 8) {
            $top->put('Other', $counts->skip(8)->sum());
        }

        $section['rows'] = $top->map(fn ($n, $label) => ['label' => $label, 'count' => $n])->values()->all();
        $section['total'] = $counts->sum();

        return $section;
    }

    private function trend(array $section, Collection $records): array
    {
        $rows = $records->whereIn('dataset', $section['datasets']);
        $grouped = $rows->groupBy('period')
            ->sortKeysUsing(fn ($a, $b) => $this->periodKey($a) <=> $this->periodKey($b));

        $measure = $section['measure'] ?? ['type' => 'count'];
        $points = $grouped->map(function (Collection $periodRows, string $period) use ($measure, $section) {
            $value = match ($measure['type']) {
                'sum' => $periodRows->sum(fn ($r) => (float) ($r[$measure['field']] ?? 0)),
                'avg' => ($valid = $periodRows->filter(fn ($r) => is_numeric($r[$measure['field']] ?? null)))->count()
                    ? round($valid->avg(fn ($r) => (float) $r[$measure['field']]), 2) : null,
                'ratio' => ($den = $periodRows->sum(fn ($r) => (float) ($r[$measure['denominator']] ?? 0))) > 0
                    ? round($periodRows->sum(fn ($r) => (float) ($r[$measure['numerator']] ?? 0)) / $den * 100, 1) : null,
                default => $periodRows->count(),
            };
            return ['period' => $period, 'value' => $value];
        })->values();

        $section['points'] = $points->all();
        // Period-over-period delta of the headline measure.
        $values = $points->pluck('value')->filter(fn ($v) => $v !== null)->values();
        $section['delta'] = $values->count() >= 2 && $values[$values->count() - 2] != 0
            ? round(($values->last() - $values[$values->count() - 2]) / abs($values[$values->count() - 2]) * 100, 1)
            : null;

        return $section;
    }

    /**
     * R10 — per-institution reporting-calendar compliance computed from the
     * portal's own workflow records (submissions, periods and audit trail).
     */
    private function submissionCompliance(array $section, array $filters, User $user): array
    {
        $period = $filters['period'] ?? 'all';
        if ($period === 'all' || preg_match('/^\d{4}$/', (string) $period)) {
            $period = Submission::select('reporting_period')->distinct()
                ->orderByDesc('reporting_period')->value('reporting_period') ?? 'all';
        }
        $section['period_used'] = $period;
        $frequency = $this->frequencyOfPeriod($period);

        // Owners: institutions (+ one row for Ministry departments)
        $owners = Institution::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('id', $user->institution_id))
            ->when(($filters['institution'] ?? 'all') !== 'all' && ! $user->isInstitutionUser() && $filters['institution'] !== 'MIT',
                fn ($q) => $q->where('code', $filters['institution']))
            ->orderBy('code')->get();

        $rows = [];
        $includeMinistry = ! $user->isInstitutionUser() && ! $user->isMinistryDepartmentUser()
            && in_array($filters['institution'] ?? 'all', ['all', 'MIT'], true);
        if ($user->isMinistryDepartmentUser()) {
            $owners = collect(); // department officers see only their own department row
            $includeMinistry = true;
        }

        $buildRow = function (?Institution $institution, ?Department $department) use ($period, $frequency) {
            $datasetQuery = Dataset::where('is_active', true);
            $submissionQuery = Submission::query();
            if ($institution) {
                $datasetQuery->where('institution_id', $institution->id);
                $submissionQuery->where('institution_id', $institution->id);
            } else {
                $datasetQuery->whereNull('institution_id');
                $submissionQuery->whereNull('institution_id');
                if ($department) {
                    $datasetQuery->where('department_id', $department->id);
                    $submissionQuery->whereHas('dataset', fn ($q) => $q->where('department_id', $department->id));
                }
            }
            if ($frequency) {
                $datasetQuery->where('frequency', $frequency);
            }

            $expected = $datasetQuery->count();
            $subs = (clone $submissionQuery)->when($period !== 'all', fn ($q) => $q->where('reporting_period', $period))->get();
            $ids = $subs->pluck('id');

            $returns = $ids->isEmpty() ? 0 : AuditLog::where('auditable_type', Submission::class)
                ->whereIn('auditable_id', $ids)
                ->whereIn('action', ['review.returned', 'internal.returned_officer', 'internal.returned_supervisor'])
                ->count();

            // Timeliness against the configured submission period deadline.
            $periodRecord = SubmissionPeriod::where('name', $period)
                ->when($institution, fn ($q) => $q->where('institution_id', $institution->id))
                ->when(! $institution, fn ($q) => $q->whereNull('institution_id'))
                ->first();
            $timely = null;
            if ($periodRecord?->closes_at && $subs->whereNotNull('submitted_at')->isNotEmpty()) {
                $deadline = Carbon::parse($periodRecord->closes_at)->endOfDay();
                $onTime = $subs->filter(fn ($s) => $s->submitted_at && Carbon::parse($s->submitted_at)->lte($deadline))->count();
                $timely = round($onTime / $subs->whereNotNull('submitted_at')->count() * 100, 1);
            }

            $received = $subs->count();
            return [
                'owner' => $institution ? $institution->code : ($department ? 'MIT / '.$department->code : 'MIT Departments'),
                'name' => $institution ? $institution->name : ($department?->name ?? 'Ministry departments'),
                'expected' => $expected,
                'received' => $received,
                'coverage' => $expected > 0 ? round(min($received, $expected) / $expected * 100, 1) : null,
                'approved' => $subs->whereIn('status', ['accepted', 'published'])->count(),
                'pipeline' => $subs->whereIn('status', ['internal_review', 'accounting_review', 'submitted', 'under_review', 'pending_approval'])->count(),
                'returned' => $subs->whereIn('status', ['returned', 'returned_officer', 'returned_supervisor', 'rejected'])->count(),
                'first_pass' => $received > 0 ? round(max(0, $received - $returns) / $received * 100, 1) : null,
                'returns' => $returns,
                'timeliness' => $timely,
            ];
        };

        foreach ($owners as $institution) {
            $row = $buildRow($institution, null);
            // Skip owners with nothing to report (no datasets at this frequency and no submissions).
            if ($row['expected'] === 0 && $row['received'] === 0) {
                continue;
            }
            $rows[] = $row;
        }
        if ($includeMinistry) {
            if ($user->isMinistryDepartmentUser()) {
                $rows[] = $buildRow(null, $user->department);
            } else {
                $rows[] = $buildRow(null, null);
            }
        }

        $section['rows'] = $rows;
        return $section;
    }

    // -------------------------------------------------------------- indicators

    private function indicator(array $ind, Collection $records): ?float
    {
        $codes = $ind['datasets'] ?? [$ind['dataset'] ?? null];
        $rows = $records->whereIn('dataset', $codes);

        return match ($ind['type']) {
            'count' => (float) $rows->count(),
            'sum' => (float) $rows->sum(fn ($r) => (float) ($r[$ind['field']] ?? 0)),
            'avg' => ($valid = $rows->filter(fn ($r) => is_numeric($r[$ind['field']] ?? null)))->count()
                ? round($valid->avg(fn ($r) => (float) $r[$ind['field']]), 2) : null,
            'ratio' => ($den = $rows->sum(fn ($r) => (float) ($r[$ind['denominator']] ?? 0))) > 0
                ? round($rows->sum(fn ($r) => (float) ($r[$ind['numerator']] ?? 0)) / $den * 100, 1) : null,
            'count_where' => (float) $this->whereMatch($rows, $ind)->count(),
            'rate_where' => $rows->count() > 0
                ? round($this->whereMatch($rows, $ind)->count() / $rows->count() * 100, 1) : null,
            'distinct' => (float) $rows->pluck($ind['field'])->filter(fn ($v) => trim((string) $v) !== '')->unique()->count(),
            'due_within' => (float) $rows->filter(function ($r) use ($ind) {
                $date = $r[$ind['field']] ?? null;
                if (! $date) return false;
                try {
                    $d = Carbon::parse($date);
                    return $d->gte(now()->startOfDay()) && $d->lte(now()->addDays($ind['days'] ?? 90)->endOfDay());
                } catch (\Throwable) {
                    return false;
                }
            })->count(),
            default => null,
        };
    }

    private function whereMatch(Collection $rows, array $ind): Collection
    {
        $values = array_map('mb_strtolower', (array) ($ind['values'] ?? [$ind['value'] ?? '']));
        $contains = ($ind['match'] ?? 'exact') === 'contains';

        return $rows->filter(function ($r) use ($ind, $values, $contains) {
            $v = mb_strtolower((string) ($r[$ind['field']] ?? ''));
            foreach ($values as $needle) {
                if ($contains ? str_contains($v, $needle) : $v === $needle) {
                    return true;
                }
            }
            return false;
        });
    }

    // ---------------------------------------------------------------- helpers

    private function format(?float $raw, string $format): string
    {
        if ($raw === null) {
            return '—';
        }
        return match ($format) {
            'percent' => number_format($raw, 1).' %',
            'money' => 'TZS '.number_format($raw),
            'usd' => 'USD '.number_format($raw, 1).' m',
            'days' => rtrim(rtrim(number_format($raw, 1), '0'), '.').' days',
            'months' => rtrim(rtrim(number_format($raw, 1), '0'), '.').' months',
            'mt' => number_format($raw).' MT',
            default => fmod($raw, 1.0) === 0.0 ? number_format($raw) : number_format($raw, 1),
        };
    }

    /** Sortable key for period names like "2026 - Q1", "2026 - M03", "2026 - Annually". */
    public function periodKey(string $period): int
    {
        if (preg_match('/(\d{4})\s*-\s*Q(\d)/', $period, $m)) {
            return (int) $m[1] * 1000 + (int) $m[2] * 10;
        }
        if (preg_match('/(\d{4})\s*-\s*M(\d{1,2})/', $period, $m)) {
            return (int) $m[1] * 1000 + (int) $m[2];
        }
        if (preg_match('/(\d{4})/', $period, $m)) {
            return (int) $m[1] * 1000 + 900; // Annually/Weekly sort after quarters
        }
        return PHP_INT_MAX;
    }

    /** Map a period name to the dataset frequency it belongs to. */
    private function frequencyOfPeriod(string $period): ?string
    {
        return match (true) {
            str_contains($period, 'Q') && preg_match('/Q\d/', $period) === 1 => 'quarterly',
            (bool) preg_match('/M\d{2}/', $period) => 'monthly',
            str_contains($period, 'Annually') => 'annually',
            str_contains($period, 'Weekly') => 'weekly',
            default => null,
        };
    }
}
