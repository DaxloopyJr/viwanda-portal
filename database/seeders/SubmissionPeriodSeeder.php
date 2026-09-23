<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Seeder;

class SubmissionPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $year = 2026;

        // Institution-scoped periods for every registered institution, plus
        // ministry-wide periods (institution_id = null) for ministry departments.
        $scopes = [null];
        foreach (Institution::orderBy('code')->get() as $institution) {
            $scopes[] = $institution->id;
        }

        foreach ($scopes as $institutionId) {
            for ($q = 1; $q <= 4; $q++) {
                SubmissionPeriod::firstOrCreate(
                    ['institution_id' => $institutionId, 'name' => sprintf('%d - Q%d', $year, $q)],
                    [
                        'frequency' => 'quarterly',
                        'year' => $year,
                        'quarter' => $q,
                        'opens_at' => sprintf('%d-%02d-01', $year, ($q - 1) * 3 + 1),
                        'closes_at' => sprintf('%d-%02d-%02d', $year, $q * 3, $q === 1 ? 31 : ($q === 2 ? 30 : ($q === 3 ? 30 : 31))),
                        'is_active' => true,
                    ]
                );
            }
            SubmissionPeriod::firstOrCreate(
                ['institution_id' => $institutionId, 'name' => "$year - Annually"],
                [
                    'frequency' => 'annually',
                    'year' => $year,
                    'opens_at' => "$year-01-01",
                    'closes_at' => "$year-12-31",
                    'is_active' => true,
                ]
            );
        }
    }
}
