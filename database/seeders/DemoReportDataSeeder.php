<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo approved/published submissions for 2026 - Q1 and 2026 - Q2 across the
 * FCT, TBS and Ministry-department catalogues, so the thematic management
 * reports (R1–R14) render meaningful indicators out of the box.
 */
class DemoReportDataSeeder extends Seeder
{
    public function run(): void
    {
        $officers = [
            'FCT' => User::where('email', 'fct.officer@fct.go.tz')->value('id'),
            'TBS' => User::where('email', 'tbs.officer@tbs.go.tz')->value('id') ?? User::where('email', 'manager@viwanda.go.tz')->value('id'),
            'MIT' => User::where('email', 'dept.officer@viwanda.go.tz')->value('id'),
        ];
        $reviewer = User::where('email', 'reviewer@viwanda.go.tz')->value('id');
        $i = 0;

        foreach ($this->plan() as [$code, $period, $status, $rows]) {
            $ds = Dataset::where('code', $code)->firstOrFail();
            $owner = $ds->institution?->code ?? 'MIT';
            // Timestamp inside the reporting window so on-time % is realistic in R10.
            $base = match (true) {
                str_contains($period, 'Q1') => ['2026-03-26 10:00:00', '2026-04-08 10:00:00', '2026-04-15 10:00:00'],
                str_contains($period, 'Q2') => ['2026-06-25 10:00:00', '2026-07-06 10:00:00', '2026-07-13 10:00:00'],
                default => ['2026-06-30 10:00:00', '2026-07-10 10:00:00', '2026-07-17 10:00:00'],
            };
            $submission = Submission::firstOrCreate(
                ['reference' => sprintf('VP-2026-%05d', 10 + $i)],
                [
                    'transaction_reference' => $code.'-'.str_replace(' ', '', $period).'-DEMO',
                    'institution_id' => $ds->institution_id,
                    'dataset_id' => $ds->id,
                    'reporting_period' => $period,
                    'channel' => 'portal',
                    'consumers' => ['MIT', 'Public'],
                    'status' => $status,
                    'submitted_by' => $officers[$owner] ?? User::where('email', 'manager@viwanda.go.tz')->value('id'),
                    'submitted_at' => \Carbon\Carbon::parse($base[0])->addHours($i),
                    'reviewed_by' => $reviewer,
                    'reviewed_at' => \Carbon\Carbon::parse($base[1])->addHours($i),
                    'published_at' => $status === 'published' ? \Carbon\Carbon::parse($base[2])->addHours($i) : null,
                    'records_count' => count($rows),
                ]
            );

            if ($submission->records()->count() === 0) {
                foreach ($rows as $n => $row) {
                    $submission->records()->create(['row_number' => $n + 1, 'data' => $row]);
                }
            }
            $i++;
        }
    }

    private function plan(): array
    {
        return [
            // FCT-CA — 2026 - Q1
            ['FCT-CA', '2026 - Q1', 'published', [
                ['date_determined' => '2026-01-28', 'case_no' => 'Appeal No. 02/2026', 'authority' => 'FCC', 'subject_matter' => 'Competition: Mergers and Acquisitions', 'determination' => 'Appeal dismissed', 'remarks' => null],
                ['date_determined' => '2026-02-18', 'case_no' => 'Application No. 04/2026', 'authority' => 'EWURA', 'subject_matter' => 'Energy: Petroleum sector', 'determination' => 'Authority decision set aside', 'remarks' => 'Remitted to EWURA'],
                ['date_determined' => '2026-03-20', 'case_no' => 'Appeal No. 07/2026', 'authority' => 'TCRA', 'subject_matter' => 'Communications: Spectrum licensing', 'determination' => 'Order delivered', 'remarks' => null]
            ]],
            // FCT-CA — 2026 - Q2
            ['FCT-CA', '2026 - Q2', 'published', [
                ['date_determined' => '2026-04-15', 'case_no' => 'Appeal No. 11/2026', 'authority' => 'LATRA', 'subject_matter' => 'Transport: Fare dispute', 'determination' => 'Appeal allowed', 'remarks' => null],
                ['date_determined' => '2026-05-22', 'case_no' => 'Appeal No. 14/2026', 'authority' => 'PURA', 'subject_matter' => 'Petroleum upstream licensing', 'determination' => 'Upheld', 'remarks' => null],
                ['date_determined' => '2026-06-19', 'case_no' => 'Application No. 16/2026', 'authority' => 'TCAA', 'subject_matter' => 'Aviation: Airport charges', 'determination' => 'Order delivered', 'remarks' => null],
                ['date_determined' => '2026-06-27', 'case_no' => 'Appeal No. 18/2026', 'authority' => 'FCC', 'subject_matter' => 'Competition: Abuse of dominance', 'determination' => 'Appeal allowed in part', 'remarks' => null]
            ]],
            // FCT-RC — 2026 - Q1
            ['FCT-RC', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'authority' => 'FCC', 'cases_resolved' => 14, 'cases_due' => 18, 'resolution_rate' => 77.8, 'remarks' => null],
                ['period' => '2026 - Q1', 'authority' => 'EWURA', 'cases_resolved' => 6, 'cases_due' => 8, 'resolution_rate' => 75.0, 'remarks' => null],
                ['period' => '2026 - Q1', 'authority' => 'TCRA', 'cases_resolved' => 5, 'cases_due' => 6, 'resolution_rate' => 83.3, 'remarks' => null],
                ['period' => '2026 - Q1', 'authority' => 'LATRA', 'cases_resolved' => 4, 'cases_due' => 7, 'resolution_rate' => 57.1, 'remarks' => 'Two complex fare disputes carried over']
            ]],
            // FCT-RC — 2026 - Q2
            ['FCT-RC', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'authority' => 'FCC', 'cases_resolved' => 17, 'cases_due' => 20, 'resolution_rate' => 85.0, 'remarks' => null],
                ['period' => '2026 - Q2', 'authority' => 'EWURA', 'cases_resolved' => 8, 'cases_due' => 9, 'resolution_rate' => 88.9, 'remarks' => null],
                ['period' => '2026 - Q2', 'authority' => 'TCRA', 'cases_resolved' => 6, 'cases_due' => 7, 'resolution_rate' => 85.7, 'remarks' => null],
                ['period' => '2026 - Q2', 'authority' => 'LATRA', 'cases_resolved' => 6, 'cases_due' => 7, 'resolution_rate' => 85.7, 'remarks' => null]
            ]],
            // FCT-ATD — 2026 - Q1
            ['FCT-ATD', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'authority' => 'FCC', 'cases_disposed' => 14, 'total_months' => 58, 'average_months' => 4.1, 'remarks' => null],
                ['period' => '2026 - Q1', 'authority' => 'EWURA', 'cases_disposed' => 6, 'total_months' => 21, 'average_months' => 3.5, 'remarks' => null],
                ['period' => '2026 - Q1', 'authority' => 'TCRA', 'cases_disposed' => 5, 'total_months' => 19, 'average_months' => 3.8, 'remarks' => null]
            ]],
            // FCT-ATD — 2026 - Q2
            ['FCT-ATD', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'authority' => 'FCC', 'cases_disposed' => 17, 'total_months' => 61, 'average_months' => 3.6, 'remarks' => null],
                ['period' => '2026 - Q2', 'authority' => 'EWURA', 'cases_disposed' => 8, 'total_months' => 26, 'average_months' => 3.3, 'remarks' => null],
                ['period' => '2026 - Q2', 'authority' => 'LATRA', 'cases_disposed' => 6, 'total_months' => 25, 'average_months' => 4.2, 'remarks' => null]
            ]],
            // FCT-CR — 2026 - Q1
            ['FCT-CR', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'complaints_received' => 32, 'complaints_resolved' => 28, 'resolved_within_timeframe' => 24, 'compliance_rate' => 75.0, 'remarks' => null]
            ]],
            // FCT-CR — 2026 - Q2
            ['FCT-CR', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'complaints_received' => 27, 'complaints_resolved' => 26, 'resolved_within_timeframe' => 23, 'compliance_rate' => 85.2, 'remarks' => null]
            ]],
            // FCT-ART — 2026 - Q1
            ['FCT-ART', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'enquiries_received' => 118, 'total_response_days' => 336, 'average_response_days' => 2.8, 'standard_days' => 3, 'compliance' => 'Yes']
            ]],
            // FCT-ART — 2026 - Q2
            ['FCT-ART', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'enquiries_received' => 134, 'total_response_days' => 348, 'average_response_days' => 2.6, 'standard_days' => 3, 'compliance' => 'Yes']
            ]],
            // FCT-OE — 2026 - Q1
            ['FCT-OE', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'cases_filed' => 41, 'cases_digital' => 29, 'system_uptime' => 98.6, 'efficiency' => 70.7, 'remarks' => null]
            ]],
            // FCT-OE — 2026 - Q2
            ['FCT-OE', '2026 - Q2', 'accepted', [
                ['period' => '2026 - Q2', 'cases_filed' => 47, 'cases_digital' => 38, 'system_uptime' => 99.2, 'efficiency' => 80.9, 'remarks' => null]
            ]],
            // FCT-RU — 2026 - Q1
            ['FCT-RU', '2026 - Q1', 'published', [
                ['date' => '2026-02-10', 'upgrade_component' => 'E-filing module', 'description' => 'Deployment of the electronic case filing module for the registry.', 'acquisition' => 'GoT budget 2025/26', 'status' => 'Completed', 'remarks' => null],
                ['date' => '2026-03-05', 'upgrade_component' => 'Registry digitisation scanner pool', 'description' => 'High-speed scanners for case-file digitisation.', 'acquisition' => 'GoT budget 2025/26', 'status' => 'In progress', 'remarks' => null]
            ]],
            // FCT-TRR — 2026 - Q1
            ['FCT-TRR', '2026 - Q1', 'published', [
                ['date' => '2026-01-30', 'rule_provision' => 'Rule 12 — Filing timelines', 'review_aspect' => 'Alignment with e-filing', 'responsible_team' => 'Rules Committee', 'status' => 'Completed', 'remarks' => null],
                ['date' => '2026-03-12', 'rule_provision' => 'Rule 27 — Hearing notices', 'review_aspect' => 'Electronic service of notices', 'responsible_team' => 'Rules Committee', 'status' => 'In progress', 'remarks' => null]
            ]],
            // FCT-TRP — 2026 - Q1
            ['FCT-TRP', '2026 - Q1', 'published', [
                ['date_published' => '2026-03-28', 'gazette_no' => 'GN No. 152/2026', 'rule_title' => 'Fair Competition Tribunal (Procedure) Rules, 2026', 'effective_date' => '2026-05-01', 'publication_media' => 'Government Gazette', 'remarks' => null]
            ]],
            // FCT-ES — 2026 - Q1
            ['FCT-ES', '2026 - Q1', 'published', [
                ['date' => '2026-02-27', 'study_title' => 'Competition Assessment of the Fertiliser Market in Tanzania', 'sector_covered' => 'Agriculture inputs', 'status' => 'Completed', 'author_team' => 'Research & Policy Unit', 'dissemination_channel' => 'Stakeholder workshop'],
                ['date' => '2026-03-25', 'study_title' => 'State of Competition in Digital Financial Services', 'sector_covered' => 'Financial services', 'status' => 'In progress', 'author_team' => 'Research & Policy Unit', 'dissemination_channel' => 'Portal publication']
            ]],
            // FCT-ES — 2026 - Q2
            ['FCT-ES', '2026 - Q2', 'accepted', [
                ['date' => '2026-05-29', 'study_title' => 'Regulated Sectors Performance Review 2025/26', 'sector_covered' => 'Energy, water, transport, communications', 'status' => 'Completed', 'author_team' => 'Research & Policy Unit', 'dissemination_channel' => 'Ministry brief']
            ]],
            // FCT-CM — 2026 - Q1
            ['FCT-CM', '2026 - Q1', 'published', [
                ['date' => '2026-02-12', 'meeting_title' => 'Regulators coordination meeting', 'stakeholder_category' => 'Sector regulators', 'venue' => 'Dodoma', 'participants' => 38, 'key_output' => 'Agreed joint referral protocol for cross-sector cases'],
                ['date' => '2026-03-18', 'meeting_title' => 'Business community consultative forum', 'stakeholder_category' => 'Private sector associations', 'venue' => 'Dar es Salaam', 'participants' => 64, 'key_output' => 'Feedback on tribunal procedures incorporated into rules review']
            ]],
            // FCT-CM — 2026 - Q2
            ['FCT-CM', '2026 - Q2', 'published', [
                ['date' => '2026-05-14', 'meeting_title' => 'Judiciary and tribunal dialogue', 'stakeholder_category' => 'Judiciary', 'venue' => 'Arusha', 'participants' => 27, 'key_output' => 'Harmonised practice directions']
            ]],
            // FCT-PA — 2026 - Q1
            ['FCT-PA', '2026 - Q1', 'published', [
                ['date' => '2026-02-06', 'activity_name' => 'Competition law awareness — universities', 'venue_region' => 'Dar es Salaam', 'target_audience' => 'Law students', 'participants' => 210, 'medium' => 'Workshop', 'key_message' => 'Role of the Tribunal in market fairness'],
                ['date' => '2026-03-13', 'activity_name' => 'SME outreach on appeal procedures', 'venue_region' => 'Mwanza', 'target_audience' => 'SMEs', 'participants' => 145, 'medium' => 'Roadshow', 'key_message' => 'How to lodge an appeal']
            ]],
            // FCT-PA — 2026 - Q2
            ['FCT-PA', '2026 - Q2', 'published', [
                ['date' => '2026-04-24', 'activity_name' => 'Media engagement on competition matters', 'venue_region' => 'Dodoma', 'target_audience' => 'Journalists', 'participants' => 52, 'medium' => 'Press briefing', 'key_message' => 'Transparency of tribunal decisions'],
                ['date' => '2026-06-11', 'activity_name' => 'Regional awareness campaign', 'venue_region' => 'Arusha', 'target_audience' => 'General public', 'participants' => 320, 'medium' => 'Exhibition', 'key_message' => 'Consumer rights and redress']
            ]],
            // TBS-NSF — 2026 - Q1
            ['TBS-NSF', '2026 - Q1', 'published', [
                ['date_approved' => '2026-01-16', 'standard_no' => 'TZS 1850:2026', 'standard_title' => 'Fortified wheat flour — Specification', 'sector' => 'Food and agriculture', 'stage' => 'Gazetted', 'status' => 'Published'],
                ['date_approved' => '2026-02-13', 'standard_no' => 'TZS 1862:2026', 'standard_title' => 'Solar photovoltaic modules — Requirements', 'sector' => 'Energy', 'stage' => 'Approved', 'status' => 'Published'],
                ['date_approved' => '2026-03-20', 'standard_no' => 'TZS 1871:2026', 'standard_title' => 'Precast concrete blocks — Specification', 'sector' => 'Construction', 'stage' => 'Approved', 'status' => 'Published']
            ]],
            // TBS-NSF — 2026 - Q2
            ['TBS-NSF', '2026 - Q2', 'published', [
                ['date_approved' => '2026-04-17', 'standard_no' => 'TZS 1880:2026', 'standard_title' => 'Packaged drinking water — Specification (rev.)', 'sector' => 'Food and agriculture', 'stage' => 'Gazetted', 'status' => 'Published'],
                ['date_approved' => '2026-05-15', 'standard_no' => 'TZS 1885:2026', 'standard_title' => 'Textiles — School uniforms', 'sector' => 'Textiles', 'stage' => 'Approved', 'status' => 'Published'],
                ['date_approved' => '2026-06-12', 'standard_no' => 'TZS 1890:2026', 'standard_title' => 'Cement — Composite cement specification', 'sector' => 'Construction', 'stage' => 'Approved', 'status' => 'Published'],
                ['date_approved' => '2026-06-26', 'standard_no' => 'TZS 1894:2026', 'standard_title' => 'Edible sunflower oil — Specification', 'sector' => 'Food and agriculture', 'stage' => 'Draft final', 'status' => 'Under approval']
            ]],
            // TBS-SS — 2026 - Q1
            ['TBS-SS', '2026 - Q1', 'published', [
                ['date' => '2026-01-22', 'standard_no' => 'TZS 109:2018', 'standard_title' => 'Cement — Specification', 'client_category' => 'Manufacturer', 'copies_sold' => 46, 'amount_tzs' => 2300000],
                ['date' => '2026-02-19', 'standard_no' => 'TZS 1850:2026', 'standard_title' => 'Fortified wheat flour', 'client_category' => 'SME', 'copies_sold' => 31, 'amount_tzs' => 1550000],
                ['date' => '2026-03-17', 'standard_no' => 'TZS 789:2020', 'standard_title' => 'Packaged drinking water', 'client_category' => 'Manufacturer', 'copies_sold' => 58, 'amount_tzs' => 2900000]
            ]],
            // TBS-SS — 2026 - Q2
            ['TBS-SS', '2026 - Q2', 'published', [
                ['date' => '2026-04-21', 'standard_no' => 'TZS 1880:2026', 'standard_title' => 'Packaged drinking water (rev.)', 'client_category' => 'SME', 'copies_sold' => 74, 'amount_tzs' => 3700000],
                ['date' => '2026-05-20', 'standard_no' => 'TZS 1222:2022', 'standard_title' => 'Steel bars for construction', 'client_category' => 'Manufacturer', 'copies_sold' => 39, 'amount_tzs' => 1950000],
                ['date' => '2026-06-16', 'standard_no' => 'TZS 1862:2026', 'standard_title' => 'Solar PV modules', 'client_category' => 'Importer', 'copies_sold' => 22, 'amount_tzs' => 1100000]
            ]],
            // TBS-QCT — 2026 - Q1
            ['TBS-QCT', '2026 - Q1', 'published', [
                ['date' => '2026-01-27', 'training_title' => 'SQMT for food processors', 'sqmt_area' => 'Quality management', 'venue_region' => 'Dar es Salaam', 'participants' => 86, 'remarks' => null],
                ['date' => '2026-03-09', 'training_title' => 'Laboratory quality systems', 'sqmt_area' => 'Testing', 'venue_region' => 'Arusha', 'participants' => 42, 'remarks' => null]
            ]],
            // TBS-QCT — 2026 - Q2
            ['TBS-QCT', '2026 - Q2', 'published', [
                ['date' => '2026-04-20', 'training_title' => 'SQMT for construction materials producers', 'sqmt_area' => 'Standardization', 'venue_region' => 'Mbeya', 'participants' => 63, 'remarks' => null],
                ['date' => '2026-05-25', 'training_title' => 'Metrology for industry', 'sqmt_area' => 'Metrology', 'venue_region' => 'Mwanza', 'participants' => 37, 'remarks' => null],
                ['date' => '2026-06-22', 'training_title' => 'HACCP implementation', 'sqmt_area' => 'Food safety', 'venue_region' => 'Dar es Salaam', 'participants' => 71, 'remarks' => null]
            ]],
            // TBS-SE — 2026 - Q1
            ['TBS-SE', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'enquiries_received' => 412, 'enquiries_responded' => 398, 'average_response_days' => 1.9, 'channel' => 'Portal', 'remarks' => null],
                ['period' => '2026 - Q1', 'enquiries_received' => 187, 'enquiries_responded' => 180, 'average_response_days' => 2.4, 'channel' => 'Email', 'remarks' => null]
            ]],
            // TBS-SE — 2026 - Q2
            ['TBS-SE', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'enquiries_received' => 455, 'enquiries_responded' => 449, 'average_response_days' => 1.6, 'channel' => 'Portal', 'remarks' => null],
                ['period' => '2026 - Q2', 'enquiries_received' => 203, 'enquiries_responded' => 197, 'average_response_days' => 2.1, 'channel' => 'Email', 'remarks' => null]
            ]],
            // TBS-PST — 2026 - Q1
            ['TBS-PST', '2026 - Q1', 'published', [
                ['date_received' => '2026-01-19', 'sample_product' => 'Bottled water', 'test_type' => 'Microbiological', 'client' => 'Kilimanjaro Water Ltd', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-02-04', 'sample_product' => 'Wheat flour', 'test_type' => 'Fortification levels', 'client' => 'Azam Millers', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-02-25', 'sample_product' => 'Cooking oil', 'test_type' => 'Chemical', 'client' => 'Murzah Oil Mills', 'test_result' => 'Fail', 'remarks' => 'Peroxide value above limit'],
                ['date_received' => '2026-03-16', 'sample_product' => 'Cement', 'test_type' => 'Compressive strength', 'client' => 'Twiga Cement', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-03-24', 'sample_product' => 'Fruit juice', 'test_type' => 'Microbiological', 'client' => 'Darsh Industries', 'test_result' => 'Pass', 'remarks' => null]
            ]],
            // TBS-PST — 2026 - Q2
            ['TBS-PST', '2026 - Q2', 'published', [
                ['date_received' => '2026-04-08', 'sample_product' => 'Maize flour', 'test_type' => 'Aflatoxin', 'client' => 'Kibaigwa Millers', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-04-29', 'sample_product' => 'Bottled water', 'test_type' => 'Microbiological', 'client' => 'Uhai Water', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-05-18', 'sample_product' => 'Steel bars', 'test_type' => 'Tensile strength', 'client' => 'KamSteel', 'test_result' => 'Fail', 'remarks' => 'Below grade 500'],
                ['date_received' => '2026-06-09', 'sample_product' => 'Yoghurt', 'test_type' => 'Microbiological', 'client' => 'AZAM Dairy', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-06-23', 'sample_product' => 'Soap', 'test_type' => 'Chemical', 'client' => 'BioSohn', 'test_result' => 'Pass', 'remarks' => null],
                ['date_received' => '2026-06-29', 'sample_product' => 'Honey', 'test_type' => 'Purity', 'client' => 'Arusha Honey Coop', 'test_result' => 'Pass', 'remarks' => null]
            ]],
            // TBS-EC — 2026 - Q1
            ['TBS-EC', '2026 - Q1', 'published', [
                ['date' => '2026-01-21', 'equipment_item' => 'Weighbridge 60t', 'client' => 'TANESCO Mtwara', 'certificate_no' => 'CAL/2026/0112', 'next_due_date' => '2027-01-21', 'remarks' => null],
                ['date' => '2026-02-11', 'equipment_item' => 'Pressure gauge set', 'client' => 'TPDC', 'certificate_no' => 'CAL/2026/0187', 'next_due_date' => '2026-11-15', 'remarks' => null],
                ['date' => '2026-03-04', 'equipment_item' => 'Laboratory balance', 'client' => 'Government Chemist', 'certificate_no' => 'CAL/2026/0241', 'next_due_date' => '2026-12-20', 'remarks' => null]
            ]],
            // TBS-EC — 2026 - Q2
            ['TBS-EC', '2026 - Q2', 'accepted', [
                ['date' => '2026-04-15', 'equipment_item' => 'Fuel dispenser meters', 'client' => 'PUMA Energy', 'certificate_no' => 'CAL/2026/0310', 'next_due_date' => '2026-10-15', 'remarks' => null],
                ['date' => '2026-05-27', 'equipment_item' => 'Torque wrenches', 'client' => 'CRJE Construction', 'certificate_no' => 'CAL/2026/0366', 'next_due_date' => '2027-05-27', 'remarks' => null]
            ]],
            // TBS-MSC — 2026 - Q1
            ['TBS-MSC', '2026 - Q1', 'published', [
                ['date' => '2026-01-30', 'company' => 'Bakhresa Food Products', 'management_system' => 'ISO 9001:2015', 'certificate_no' => 'MS/2026/0041', 'validity_date' => '2029-01-30', 'status' => 'Issued'],
                ['date' => '2026-03-13', 'company' => 'Serena Hotels', 'management_system' => 'ISO 22000:2018', 'certificate_no' => 'MS/2026/0052', 'validity_date' => '2026-11-20', 'status' => 'Issued']
            ]],
            // TBS-MSC — 2026 - Q2
            ['TBS-MSC', '2026 - Q2', 'published', [
                ['date' => '2026-04-24', 'company' => 'Tanga Cement', 'management_system' => 'ISO 9001:2015', 'certificate_no' => 'MS/2026/0063', 'validity_date' => '2029-04-24', 'status' => 'Issued'],
                ['date' => '2026-06-05', 'company' => 'Mohammed Enterprises', 'management_system' => 'ISO 14001:2015', 'certificate_no' => 'MS/2026/0071', 'validity_date' => '2029-06-05', 'status' => 'Issued']
            ]],
            // TBS-FCR — 2026 - Q1
            ['TBS-FCR', '2026 - Q1', 'published', [
                ['date' => '2026-01-14', 'applicant' => 'Fresha Foods', 'product_name' => 'Mango juice 500ml', 'category' => 'Food', 'permit_no' => 'FCR/2026/0122', 'status' => 'Issued'],
                ['date' => '2026-02-09', 'applicant' => 'Zanzibar Cosmetics', 'product_name' => 'Clove soap', 'category' => 'Cosmetic', 'permit_no' => 'FCR/2026/0155', 'status' => 'Issued'],
                ['date' => '2026-03-11', 'applicant' => 'Kilimanjaro Water Ltd', 'product_name' => 'Sparkling water 1L', 'category' => 'Food', 'permit_no' => 'FCR/2026/0198', 'status' => 'Issued']
            ]],
            // TBS-FCR — 2026 - Q2
            ['TBS-FCR', '2026 - Q2', 'published', [
                ['date' => '2026-04-16', 'applicant' => 'Mama Ntilie Coop', 'product_name' => 'Pre-cooked beans', 'category' => 'Food', 'permit_no' => 'FCR/2026/0234', 'status' => 'Issued'],
                ['date' => '2026-05-12', 'applicant' => 'Pembe Herbal', 'product_name' => 'Herbal lotion', 'category' => 'Cosmetic', 'permit_no' => 'FCR/2026/0267', 'status' => 'Rejected'],
                ['date' => '2026-06-18', 'applicant' => 'Dodoma Winery', 'product_name' => 'Grape juice', 'category' => 'Food', 'permit_no' => 'FCR/2026/0291', 'status' => 'Issued']
            ]],
            // TBS-PRP — 2026 - Q1
            ['TBS-PRP', '2026 - Q1', 'published', [
                ['date' => '2026-01-20', 'premises_name' => 'Fresha Foods plant', 'owner' => 'Fresha Foods', 'location' => 'Dar es Salaam', 'permit_no' => 'PRP/2026/0081', 'status' => 'Issued'],
                ['date' => '2026-03-02', 'premises_name' => 'Kibaha bakery', 'owner' => 'Kibaha Bakers Ltd', 'location' => 'Coast Region', 'permit_no' => 'PRP/2026/0096', 'status' => 'Issued']
            ]],
            // TBS-PRP — 2026 - Q2
            ['TBS-PRP', '2026 - Q2', 'published', [
                ['date' => '2026-04-22', 'premises_name' => 'Mama Ntilie kitchen', 'owner' => 'Mama Ntilie Coop', 'location' => 'Morogoro', 'permit_no' => 'PRP/2026/0114', 'status' => 'Issued'],
                ['date' => '2026-06-08', 'premises_name' => 'Arusha dairy plant', 'owner' => 'AZAM Dairy', 'location' => 'Arusha', 'permit_no' => 'PRP/2026/0127', 'status' => 'Issued']
            ]],
            // TBS-FRA — 2026 - Q1
            ['TBS-FRA', '2026 - Q1', 'published', [
                ['date' => '2026-02-05', 'assessment_title' => 'Aflatoxin surveillance in maize flour, Lake Zone', 'product_hazard' => 'Aflatoxin B1', 'risk_level' => 'High', 'recommendations' => 'Intensify mill inspections; recall two non-compliant brands', 'status' => 'Completed'],
                ['date' => '2026-03-19', 'assessment_title' => 'Microbiological safety of street-vended juices', 'product_hazard' => 'E. coli', 'risk_level' => 'Medium', 'recommendations' => 'Vendor hygiene training', 'status' => 'Completed']
            ]],
            // TBS-FRA — 2026 - Q2
            ['TBS-FRA', '2026 - Q2', 'published', [
                ['date' => '2026-05-07', 'assessment_title' => 'Heavy metals in imported cosmetics', 'product_hazard' => 'Mercury', 'risk_level' => 'High', 'recommendations' => 'Border alert to TRA; product seizure protocol', 'status' => 'Completed'],
                ['date' => '2026-06-16', 'assessment_title' => 'Pesticide residues in horticulture exports', 'product_hazard' => 'Chlorpyrifos', 'risk_level' => 'Low', 'recommendations' => 'Continue routine monitoring', 'status' => 'Completed']
            ]],
            // TBS-FM — 2026 - Q1
            ['TBS-FM', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'station_depot' => 'TIPER Depot Dar', 'volume_marked' => 152000000, 'samples_taken' => 86, 'samples_passed' => 82, 'remarks' => null],
                ['period' => '2026 - Q1', 'station_depot' => 'Mwanza inland depot', 'volume_marked' => 48000000, 'samples_taken' => 34, 'samples_passed' => 33, 'remarks' => null]
            ]],
            // TBS-FM — 2026 - Q2
            ['TBS-FM', '2026 - Q2', 'published', [
                ['period' => '2026 - Q2', 'station_depot' => 'TIPER Depot Dar', 'volume_marked' => 164000000, 'samples_taken' => 91, 'samples_passed' => 88, 'remarks' => null],
                ['period' => '2026 - Q2', 'station_depot' => 'Arusha satellite depot', 'volume_marked' => 39000000, 'samples_taken' => 29, 'samples_passed' => 29, 'remarks' => null]
            ]],
            // TBS-PVOC — 2026 - Q1
            ['TBS-PVOC', '2026 - Q1', 'published', [
                ['date' => '2026-01-12', 'coc_no' => 'COC/2026/04411', 'importer' => 'Hai Motors', 'product' => 'Motor vehicle spares', 'country_of_origin' => 'Japan', 'status' => 'Issued'],
                ['date' => '2026-02-03', 'coc_no' => 'COC/2026/04902', 'importer' => 'SBT Tanzania', 'product' => 'Used electronics', 'country_of_origin' => 'China', 'status' => 'Issued'],
                ['date' => '2026-03-09', 'coc_no' => 'COC/2026/05210', 'importer' => 'Rafiki Traders', 'product' => 'Textiles', 'country_of_origin' => 'India', 'status' => 'Issued'],
                ['date' => '2026-03-23', 'coc_no' => 'COC/2026/05344', 'importer' => 'Keda Ceramics', 'product' => 'Tiles', 'country_of_origin' => 'China', 'status' => 'Rejected']
            ]],
            // TBS-PVOC — 2026 - Q2
            ['TBS-PVOC', '2026 - Q2', 'published', [
                ['date' => '2026-04-14', 'coc_no' => 'COC/2026/05871', 'importer' => 'Hai Motors', 'product' => 'Tyres', 'country_of_origin' => 'Japan', 'status' => 'Issued'],
                ['date' => '2026-05-06', 'coc_no' => 'COC/2026/06120', 'importer' => 'Azam Trading', 'product' => 'Wheat flour', 'country_of_origin' => 'Turkiye', 'status' => 'Issued'],
                ['date' => '2026-05-28', 'coc_no' => 'COC/2026/06317', 'importer' => 'SBT Tanzania', 'product' => 'Solar panels', 'country_of_origin' => 'China', 'status' => 'Issued'],
                ['date' => '2026-06-17', 'coc_no' => 'COC/2026/06552', 'importer' => 'Umoja Imports', 'product' => 'Cooking oil', 'country_of_origin' => 'Malaysia', 'status' => 'Issued'],
                ['date' => '2026-06-25', 'coc_no' => 'COC/2026/06601', 'importer' => 'Rafiki Traders', 'product' => 'Footwear', 'country_of_origin' => 'Vietnam', 'status' => 'Issued']
            ]],
            // TBS-DI — 2026 - Q1
            ['TBS-DI', '2026 - Q1', 'published', [
                ['date' => '2026-01-26', 'batch_certificate_no' => 'DI/2026/00712', 'importer' => 'Rafiki Traders', 'product' => 'Second-hand clothing', 'entry_point' => 'Dar es Salaam Port', 'status' => 'Issued'],
                ['date' => '2026-03-02', 'batch_certificate_no' => 'DI/2026/00803', 'importer' => 'JKT Procurement', 'product' => 'Building materials', 'entry_point' => 'Tanga Port', 'status' => 'Issued']
            ]],
            // TBS-DI — 2026 - Q2
            ['TBS-DI', '2026 - Q2', 'published', [
                ['date' => '2026-04-20', 'batch_certificate_no' => 'DI/2026/00956', 'importer' => 'Umoja Imports', 'product' => 'Edible oils', 'entry_point' => 'Dar es Salaam Port', 'status' => 'Issued'],
                ['date' => '2026-06-01', 'batch_certificate_no' => 'DI/2026/01044', 'importer' => 'Keda Ceramics', 'product' => 'Sanitary ware', 'entry_point' => 'Dar es Salaam Port', 'status' => 'Issued']
            ]],
            // TBS-UMV — 2026 - Q1
            ['TBS-UMV', '2026 - Q1', 'published', [
                ['date' => '2026-01-15', 'certificate_no' => 'UMV/2026/0311', 'importer' => 'Hai Motors', 'vehicle' => 'Toyota Land Cruiser', 'year_of_manufacture' => 2019, 'status' => 'Issued'],
                ['date' => '2026-02-12', 'certificate_no' => 'UMV/2026/0388', 'importer' => 'Arusha Auto', 'vehicle' => 'Isuzu D-Max', 'year_of_manufacture' => 2018, 'status' => 'Issued'],
                ['date' => '2026-03-10', 'certificate_no' => 'UMV/2026/0452', 'importer' => 'Dar Auto World', 'vehicle' => 'Mitsubishi Canter', 'year_of_manufacture' => 2017, 'status' => 'Issued']
            ]],
            // TBS-UMV — 2026 - Q2
            ['TBS-UMV', '2026 - Q2', 'accepted', [
                ['date' => '2026-04-09', 'certificate_no' => 'UMV/2026/0523', 'importer' => 'Hai Motors', 'vehicle' => 'Toyota Hiace', 'year_of_manufacture' => 2019, 'status' => 'Issued'],
                ['date' => '2026-05-21', 'certificate_no' => 'UMV/2026/0587', 'importer' => 'Moshi Motors', 'vehicle' => 'Scania bus', 'year_of_manufacture' => 2016, 'status' => 'Issued']
            ]],
            // TBS-TAE — 2026 - Q1
            ['TBS-TAE', '2026 - Q1', 'published', [
                ['date' => '2026-02-17', 'exporter_company' => 'Kilombero Sugar', 'assistance_type' => 'Market requirements advisory', 'product_sector' => 'Sugar', 'destination_market' => 'EU', 'remarks' => null],
                ['date' => '2026-03-16', 'exporter_company' => 'Tanzania Horticultural Association', 'assistance_type' => 'Standards compliance training', 'product_sector' => 'Horticulture', 'destination_market' => 'EU, Middle East', 'remarks' => null]
            ]],
            // TBS-TAE — 2026 - Q2
            ['TBS-TAE', '2026 - Q2', 'published', [
                ['date' => '2026-04-27', 'exporter_company' => 'Zanzibar Seaweed Coop', 'assistance_type' => 'Product testing support', 'product_sector' => 'Seaweed', 'destination_market' => 'USA', 'remarks' => null],
                ['date' => '2026-06-15', 'exporter_company' => 'Karibu Cashews', 'assistance_type' => 'Certification guidance', 'product_sector' => 'Cashew', 'destination_market' => 'India', 'remarks' => null]
            ]],
            // TBS-WC — 2026 - Q1
            ['TBS-WC', '2026 - Q1', 'published', [
                ['date' => '2026-01-22', 'vessel_cargo' => 'MT Alpine Trader — edible oil', 'cargo_type' => 'Edible oil', 'quantity_mt' => 18500, 'inspection_result' => 'Conforming', 'remarks' => null],
                ['date' => '2026-02-26', 'vessel_cargo' => 'MT Ocean Star — diesel', 'cargo_type' => 'Petroleum products', 'quantity_mt' => 42000, 'inspection_result' => 'Conforming', 'remarks' => null],
                ['date' => '2026-03-19', 'vessel_cargo' => 'MT Lake Queen — molasses', 'cargo_type' => 'Molasses', 'quantity_mt' => 9200, 'inspection_result' => 'Non-conforming', 'remarks' => 'Sediment above specification']
            ]],
            // TBS-WC — 2026 - Q2
            ['TBS-WC', '2026 - Q2', 'published', [
                ['date' => '2026-04-23', 'vessel_cargo' => 'MT Harbour View — palm oil', 'cargo_type' => 'Edible oil', 'quantity_mt' => 21000, 'inspection_result' => 'Conforming', 'remarks' => null],
                ['date' => '2026-05-28', 'vessel_cargo' => 'MT Indian Crest — petrol', 'cargo_type' => 'Petroleum products', 'quantity_mt' => 38500, 'inspection_result' => 'Conforming', 'remarks' => null]
            ]],
            // MIT-ILR — 2026 - Q1
            ['MIT-ILR', '2026 - Q1', 'published', [
                ['date' => '2026-01-20', 'business_name' => 'Kilimanjaro Steel Works Ltd', 'licence_type' => 'Manufacturing', 'region' => 'Dar es Salaam', 'capital_investment' => 4500, 'employment' => 120, 'status' => 'New', 'remarks' => null],
                ['date' => '2026-02-24', 'business_name' => 'Mbeya Agro Packers', 'licence_type' => 'Agro-processing', 'region' => 'Mbeya', 'capital_investment' => 950, 'employment' => 46, 'status' => 'New', 'remarks' => null]
            ]],
            // MIT-IPS — 2026 - Q1
            ['MIT-IPS', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'subsector' => 'Cement and construction materials', 'production_volume' => 580000, 'unit' => 'tonnes', 'capacity_utilization' => 78.5, 'remarks' => null],
                ['period' => '2026 - Q1', 'subsector' => 'Beverages', 'production_volume' => 112000, 'unit' => 'kilolitres', 'capacity_utilization' => 83.2, 'remarks' => null]
            ]],
            // MIT-TFS — 2026 - Q1
            ['MIT-TFS', '2026 - Q1', 'published', [
                ['period' => '2026 - Q1', 'border_post' => 'Rusumo', 'exports_value_musd' => 61.4, 'imports_value_musd' => 38.2, 'clearance_time_days' => 1.8, 'remarks' => null],
                ['period' => '2026 - Q1', 'border_post' => 'Namanga', 'exports_value_musd' => 44.7, 'imports_value_musd' => 51.9, 'clearance_time_days' => 2.4, 'remarks' => null]
            ]],
            // MIT-EPA — 2026 - Q1
            ['MIT-EPA', '2026 - Q1', 'published', [
                ['date' => '2026-03-12', 'activity' => 'Tanzania trade pavilion — Gulfood 2026', 'sector' => 'Food and beverages', 'participating_firms' => 24, 'export_deals_value_musd' => 7.8, 'remarks' => null]
            ]],
            // MIT-SPR — 2026 - Annually
            ['MIT-SPR', '2026 - Annually', 'accepted', [
                ['year' => 2025, 'sector' => 'Manufacturing', 'gdp_contribution_pct' => 8.6, 'employment' => 412000, 'growth_rate_pct' => 5.4, 'remarks' => null],
                ['year' => 2025, 'sector' => 'Trade and repair', 'gdp_contribution_pct' => 9.9, 'employment' => 1850000, 'growth_rate_pct' => 4.1, 'remarks' => null]
            ]],
['BRELA-REG', '2026 - Q1', 'published', [
                    [
                        'new_companies_registered' => 2410,
                        'companies_closed' => 185,
                        'beneficial_ownership_records' => 1620,
                        'new_business_names' => 5310,
                        'industrial_licences_issued' => 96,
                        'registration_certificates_issued' => 2380,
                        'class_a_business_licences' => 412,
                        'trademarks_registered' => 648,
                        'patents_granted' => 21,
                        'remarks' => null
                    ]
                ]],
['BRELA-REG', '2026 - Q2', 'published', [
                    [
                        'new_companies_registered' => 2685,
                        'companies_closed' => 203,
                        'beneficial_ownership_records' => 1795,
                        'new_business_names' => 5840,
                        'industrial_licences_issued' => 112,
                        'registration_certificates_issued' => 2610,
                        'class_a_business_licences' => 455,
                        'trademarks_registered' => 701,
                        'patents_granted' => 27,
                        'remarks' => null
                    ]
                ]],
['WMA-LM', '2026 - Q1', 'published', [
                    [
                        'instruments_verified' => 48200,
                        'regions_reverified' => 12,
                        'secondary_standards_calibrated' => 86,
                        'new_patterns_approved' => 4,
                        'inspection_kits_procured' => 30,
                        'prepacked_goods_inspections' => 1240,
                        'routine_surprise_inspections' => 310,
                        'cotton_scale_inspections' => 96,
                        'cashew_scale_inspections' => 54,
                        'border_compliance_verifications' => 78,
                        'licence_applicants_examined' => 145,
                        'cases_prosecuted' => 12,
                        'offences_compounded' => 88,
                        'awareness_seminars' => 24,
                        'public_exhibitions' => 5,
                        'remarks' => null
                    ]
                ]],
['WMA-LM', '2026 - Q2', 'published', [
                    [
                        'instruments_verified' => 51450,
                        'regions_reverified' => 14,
                        'secondary_standards_calibrated' => 92,
                        'new_patterns_approved' => 6,
                        'inspection_kits_procured' => 18,
                        'prepacked_goods_inspections' => 1390,
                        'routine_surprise_inspections' => 352,
                        'cotton_scale_inspections' => 112,
                        'cashew_scale_inspections' => 61,
                        'border_compliance_verifications' => 84,
                        'licence_applicants_examined' => 168,
                        'cases_prosecuted' => 9,
                        'offences_compounded' => 95,
                        'awareness_seminars' => 31,
                        'public_exhibitions' => 7,
                        'remarks' => null
                    ]
                ]],
['NDC-PROJ', '2026 - Q1', 'published', [
                    [
                        'project' => 'Engaruka Soda Ash',
                        'milestone' => 'Investor negotiations round 3 concluded',
                        'meetings_held' => 4,
                        'funds_mobilised_tzs' => 2400000000,
                        'status' => 'On track',
                        'remarks' => null
                    ],
                    [
                        'project' => 'Mchuchuma-Liganga',
                        'milestone' => 'Boundary demarcation of compensated area 80% complete',
                        'meetings_held' => 3,
                        'funds_mobilised_tzs' => 1100000000,
                        'status' => 'Delayed',
                        'remarks' => 'Compensation verification pending'
                    ],
                    [
                        'project' => 'KMTC Industrial City',
                        'milestone' => 'Steel grinding ball skew rolling machines procured',
                        'meetings_held' => 2,
                        'funds_mobilised_tzs' => 5800000000,
                        'status' => 'On track',
                        'remarks' => null
                    ],
                    [
                        'project' => 'Rubber Estates',
                        'milestone' => 'Kalunga & Kihuhwi dried rubber production resumed',
                        'meetings_held' => 1,
                        'funds_mobilised_tzs' => 650000000,
                        'status' => 'On track',
                        'remarks' => null
                    ]
                ]],
['NDC-PROJ', '2026 - Q2', 'published', [
                    [
                        'project' => 'Engaruka Soda Ash',
                        'milestone' => 'Strategic investor term sheet signed',
                        'meetings_held' => 5,
                        'funds_mobilised_tzs' => 3200000000,
                        'status' => 'On track',
                        'remarks' => null
                    ],
                    [
                        'project' => 'Mchuchuma-Liganga',
                        'milestone' => 'Investor negotiation meetings held',
                        'meetings_held' => 4,
                        'funds_mobilised_tzs' => 900000000,
                        'status' => 'At risk',
                        'remarks' => 'Investor due diligence extended'
                    ],
                    [
                        'project' => 'Katewaka Coal',
                        'milestone' => 'Stakeholder meetings conducted',
                        'meetings_held' => 3,
                        'funds_mobilised_tzs' => 300000000,
                        'status' => 'On track',
                        'remarks' => null
                    ],
                    [
                        'project' => 'Nyanza Glass',
                        'milestone' => 'Godown rehabilitation completed',
                        'meetings_held' => 1,
                        'funds_mobilised_tzs' => 450000000,
                        'status' => 'Completed',
                        'remarks' => null
                    ]
                ]],
['NDC-OPS', '2026 - Q1', 'published', [
                    [
                        'machines_produced' => 14,
                        'spare_parts_produced' => 2350,
                        'rubber_produced_kg' => 182000,
                        'rubber_plantation_ha' => 1240,
                        'industrial_parks_maintained' => 3,
                        'investment_properties_rehabilitated' => 4,
                        'projects_monitored' => 11,
                        'funds_mobilised_tzs' => 9950000000,
                        'dividends_disbursed_tzs' => 1250000000,
                        'remarks' => null
                    ]
                ]],
['NDC-OPS', '2026 - Q2', 'published', [
                    [
                        'machines_produced' => 18,
                        'spare_parts_produced' => 2680,
                        'rubber_produced_kg' => 205000,
                        'rubber_plantation_ha' => 1310,
                        'industrial_parks_maintained' => 3,
                        'investment_properties_rehabilitated' => 3,
                        'projects_monitored' => 12,
                        'funds_mobilised_tzs' => 11400000000,
                        'dividends_disbursed_tzs' => 1400000000,
                        'remarks' => null
                    ]
                ]],
['CBE-EDU', '2026 - Q1', 'published', [
                    [
                        'students_enrolled' => 6840,
                        'students_field_supervised' => 1920,
                        'industrial_fields_solicited' => 86,
                        'new_programmes' => 3,
                        'programmes_reviewed' => 7,
                        'tracer_studies' => 2,
                        'research_projects' => 9,
                        'research_funds_tzs' => 480000000,
                        'publications_local' => 12,
                        'publications_international' => 5,
                        'consultancies' => 14,
                        'short_courses' => 22,
                        'incubation_programmes' => 4,
                        'entrepreneurship_clubs' => 11,
                        'youth_practitioners_trained' => 640,
                        'mous_signed' => 3,
                        'funds_mobilised_tzs' => 920000000,
                        'remarks' => null
                    ]
                ]],
['CBE-EDU', '2026 - Q2', 'published', [
                    [
                        'students_enrolled' => 7120,
                        'students_field_supervised' => 2040,
                        'industrial_fields_solicited' => 94,
                        'new_programmes' => 2,
                        'programmes_reviewed' => 5,
                        'tracer_studies' => 1,
                        'research_projects' => 11,
                        'research_funds_tzs' => 610000000,
                        'publications_local' => 15,
                        'publications_international' => 7,
                        'consultancies' => 17,
                        'short_courses' => 26,
                        'incubation_programmes' => 5,
                        'entrepreneurship_clubs' => 13,
                        'youth_practitioners_trained' => 720,
                        'mous_signed' => 4,
                        'funds_mobilised_tzs' => 1050000000,
                        'remarks' => null
                    ]
                ]],
['WRRB-WRS', '2026 - Q1', 'published', [
                    [
                        'warehouses_inspected' => 68,
                        'compliance_inspections' => 54,
                        'performance_audits' => 16,
                        'commodities_traded_wrs' => 9,
                        'stakeholder_workshops' => 12,
                        'revenue_collected_tzs' => 1840000000,
                        'receivables_collected_tzs' => 620000000,
                        'remarks' => null
                    ]
                ]],
['WRRB-WRS', '2026 - Q2', 'published', [
                    [
                        'warehouses_inspected' => 74,
                        'compliance_inspections' => 61,
                        'performance_audits' => 18,
                        'commodities_traded_wrs' => 11,
                        'stakeholder_workshops' => 15,
                        'revenue_collected_tzs' => 2050000000,
                        'receivables_collected_tzs' => 710000000,
                        'remarks' => null
                    ]
                ]],
['TANTRADE-PRO', '2026 - Q1', 'published', [
                    [
                        'value_chain_assessments' => 6,
                        'research_activities' => 8,
                        'market_info_datasets' => 34,
                        'company_profiles_published' => 412,
                        'smes_trained_male' => 380,
                        'smes_trained_female' => 465,
                        'enterprises_coached' => 210,
                        'business_clinics' => 26,
                        'trade_mission_participants' => 48,
                        'b2b_meetings' => 132,
                        'border_markets_strengthened' => 4,
                        'crossborder_programmes' => 3,
                        'remarks' => null
                    ]
                ]],
['TANTRADE-PRO', '2026 - Q2', 'published', [
                    [
                        'value_chain_assessments' => 8,
                        'research_activities' => 9,
                        'market_info_datasets' => 41,
                        'company_profiles_published' => 476,
                        'smes_trained_male' => 425,
                        'smes_trained_female' => 530,
                        'enterprises_coached' => 248,
                        'business_clinics' => 31,
                        'trade_mission_participants' => 62,
                        'b2b_meetings' => 158,
                        'border_markets_strengthened' => 5,
                        'crossborder_programmes' => 4,
                        'remarks' => null
                    ]
                ]],
['FCC-CPE', '2026 - Q1', 'published', [
                    [
                        'enforcement_actions' => 26,
                        'final_findings' => 18,
                        'complaints_investigated' => 142,
                        'complaints_resolved' => 118,
                        'consumer_complaints_redressed' => 104,
                        'appeals_coordinated' => 7,
                        'mergers_investigated' => 9,
                        'exemptions_investigated' => 4,
                        'research_studies' => 3,
                        'advocacy_sessions' => 21,
                        'sfcc_reviewed' => 38,
                        'sfcc_approved' => 29,
                        'price_surveillance' => 46,
                        'market_surveillance' => 52,
                        'raids' => 11,
                        'inspections' => 230,
                        'seizures' => 14,
                        'counterfeit_disposals' => 5,
                        'remarks' => null
                    ]
                ]],
['FCC-CPE', '2026 - Q2', 'published', [
                    [
                        'enforcement_actions' => 31,
                        'final_findings' => 22,
                        'complaints_investigated' => 158,
                        'complaints_resolved' => 139,
                        'consumer_complaints_redressed' => 121,
                        'appeals_coordinated' => 9,
                        'mergers_investigated' => 11,
                        'exemptions_investigated' => 5,
                        'research_studies' => 4,
                        'advocacy_sessions' => 26,
                        'sfcc_reviewed' => 44,
                        'sfcc_approved' => 35,
                        'price_surveillance' => 53,
                        'market_surveillance' => 61,
                        'raids' => 14,
                        'inspections' => 262,
                        'seizures' => 18,
                        'counterfeit_disposals' => 7,
                        'remarks' => null
                    ]
                ]],
['TEMDO-ENG', '2026 - Q1', 'published', [
                    [
                        'prototypes_developed' => 7,
                        'machines_deployed' => 12,
                        'incinerators_installed' => 5,
                        'medical_equipment_fabricated' => 9,
                        'pilot_technologies' => 4,
                        'contract_manufacturing_jobs' => 16,
                        'markets_reached' => 8,
                        'proposals_prepared' => 6,
                        'remarks' => null
                    ]
                ]],
['TEMDO-ENG', '2026 - Q2', 'published', [
                    [
                        'prototypes_developed' => 9,
                        'machines_deployed' => 15,
                        'incinerators_installed' => 7,
                        'medical_equipment_fabricated' => 12,
                        'pilot_technologies' => 6,
                        'contract_manufacturing_jobs' => 19,
                        'markets_reached' => 11,
                        'proposals_prepared' => 8,
                        'remarks' => null
                    ]
                ]],
['CAMARTEC-AGT', '2026 - Q1', 'published', [
                    [
                        'technologies_fabricated' => 11,
                        'prototypes_tested' => 6,
                        'mobile_factories_established' => 2,
                        'farmers_trained' => 840,
                        'machines_inspected_tested' => 48,
                        'consultancy_sessions' => 36,
                        'remarks' => null
                    ]
                ]],
['CAMARTEC-AGT', '2026 - Q2', 'published', [
                    [
                        'technologies_fabricated' => 13,
                        'prototypes_tested' => 8,
                        'mobile_factories_established' => 3,
                        'farmers_trained' => 960,
                        'machines_inspected_tested' => 57,
                        'consultancy_sessions' => 42,
                        'remarks' => null
                    ]
                ]],
['TIRDO-RDI', '2026 - Q1', 'published', [
                    [
                        'vulnerability_assessments' => 14,
                        'partnerships_established' => 5,
                        'trainings_conducted' => 9,
                        'industrial_visits' => 22,
                        'proposal_workshops' => 4,
                        'remarks' => null
                    ]
                ]],
['TIRDO-RDI', '2026 - Q2', 'published', [
                    [
                        'vulnerability_assessments' => 17,
                        'partnerships_established' => 6,
                        'trainings_conducted' => 11,
                        'industrial_visits' => 26,
                        'proposal_workshops' => 5,
                        'remarks' => null
                    ]
                ]],
['SIDO-CRD', '2026 - Q1', 'published', [
                    [
                        'new_small_medium_industries' => 64,
                        'startups_loaned' => 38,
                        'innovations_loaned' => 9,
                        'small_businesses_loaned' => 412,
                        'youth_loaned' => 296,
                        'loans_value_tzs_bn' => 18.4,
                        'youth_skills_trained' => 2150,
                        'new_direct_jobs' => 1980,
                        'npl_percent' => 6.8,
                        'entrepreneurship_campaigns' => 14,
                        'remarks' => null
                    ]
                ]],
['SIDO-CRD', '2026 - Q2', 'published', [
                    [
                        'new_small_medium_industries' => 72,
                        'startups_loaned' => 45,
                        'innovations_loaned' => 12,
                        'small_businesses_loaned' => 468,
                        'youth_loaned' => 340,
                        'loans_value_tzs_bn' => 21.7,
                        'youth_skills_trained' => 2420,
                        'new_direct_jobs' => 2260,
                        'npl_percent' => 6.2,
                        'entrepreneurship_campaigns' => 17,
                        'remarks' => null
                    ]
                ]],
['MIT-IND-CAP', '2026 - Q1', 'published', [
                    [
                        'industry_type' => 'Cement (saruji)',
                        'production_capacity' => 7200000,
                        'actual_production' => 5310000,
                        'actual_demand' => 6050000,
                        'direct_jobs' => 9400,
                        'indirect_jobs' => 38200,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Sugar (sukari)',
                        'production_capacity' => 560000,
                        'actual_production' => 412000,
                        'actual_demand' => 770000,
                        'direct_jobs' => 31000,
                        'indirect_jobs' => 88000,
                        'status' => 'Operating',
                        'remarks' => 'Domestic gap covered by imports'
                    ],
                    [
                        'industry_type' => 'Bati (roofing sheets)',
                        'production_capacity' => 310000,
                        'actual_production' => 246000,
                        'actual_demand' => 225000,
                        'direct_jobs' => 5200,
                        'indirect_jobs' => 14000,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Edible oils',
                        'production_capacity' => 480000,
                        'actual_production' => 198000,
                        'actual_demand' => 650000,
                        'direct_jobs' => 12800,
                        'indirect_jobs' => 45000,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Closed industries',
                        'production_capacity' => 150000,
                        'actual_production' => 0,
                        'actual_demand' => 0,
                        'direct_jobs' => 0,
                        'indirect_jobs' => 0,
                        'status' => 'Closed',
                        'remarks' => '12 closed factories under revival review'
                    ]
                ]],
['MIT-IND-CAP', '2026 - Q2', 'published', [
                    [
                        'industry_type' => 'Cement (saruji)',
                        'production_capacity' => 7350000,
                        'actual_production' => 5590000,
                        'actual_demand' => 6180000,
                        'direct_jobs' => 9650,
                        'indirect_jobs' => 39500,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Sugar (sukari)',
                        'production_capacity' => 570000,
                        'actual_production' => 438000,
                        'actual_demand' => 782000,
                        'direct_jobs' => 31400,
                        'indirect_jobs' => 90000,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Cashew processing (korosho)',
                        'production_capacity' => 120000,
                        'actual_production' => 86000,
                        'actual_demand' => 110000,
                        'direct_jobs' => 7800,
                        'indirect_jobs' => 22000,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Edible oils',
                        'production_capacity' => 495000,
                        'actual_production' => 215000,
                        'actual_demand' => 668000,
                        'direct_jobs' => 13200,
                        'indirect_jobs' => 47000,
                        'status' => 'Operating',
                        'remarks' => null
                    ],
                    [
                        'industry_type' => 'Closed industries',
                        'production_capacity' => 150000,
                        'actual_production' => 0,
                        'actual_demand' => 0,
                        'direct_jobs' => 0,
                        'indirect_jobs' => 0,
                        'status' => 'Closed',
                        'remarks' => '11 closed; 1 revived in Q2'
                    ]
                ]],
['MIT-IND-DEV', '2026 - Q1', 'published', [
                    [
                        'strategies_reviewed' => 3,
                        'diagnostic_studies' => 2,
                        'industrial_surveys' => 1,
                        'investor_research_studies' => 2,
                        'kaizen_interventions' => 14,
                        'consultative_forums' => 6,
                        'rd_collaborations' => 5,
                        'projects_coordinated' => 9,
                        'remarks' => null
                    ]
                ]],
['MIT-IND-DEV', '2026 - Q2', 'published', [
                    [
                        'strategies_reviewed' => 4,
                        'diagnostic_studies' => 3,
                        'industrial_surveys' => 2,
                        'investor_research_studies' => 2,
                        'kaizen_interventions' => 18,
                        'consultative_forums' => 8,
                        'rd_collaborations' => 6,
                        'projects_coordinated' => 10,
                        'remarks' => null
                    ]
                ]],
['MIT-PPP-PERF', '2026 - Q1', 'published', [
                    [
                        'objective_code' => 'Y',
                        'target' => 'Industrial sector contribution to GDP increased',
                        'activity' => 'Coordinate implementation of industrial development programmes',
                        'output_indicator' => 'Programmes coordinated',
                        'baseline' => 8,
                        'annual_target' => 12,
                        'actual_value' => 3,
                        'planned_budget_tzs' => 1800000000,
                        'disbursed_tzs' => 420000000,
                        'expenditure_tzs' => 395000000,
                        'key_activity' => 'Yes'
                    ],
                    [
                        'objective_code' => 'X',
                        'target' => 'Trade facilitation and market access improved',
                        'activity' => 'Coordinate trade negotiations and NTB elimination',
                        'output_indicator' => 'Negotiations supported',
                        'baseline' => 10,
                        'annual_target' => 16,
                        'actual_value' => 4,
                        'planned_budget_tzs' => 950000000,
                        'disbursed_tzs' => 230000000,
                        'expenditure_tzs' => 218000000,
                        'key_activity' => 'Yes'
                    ],
                    [
                        'objective_code' => 'AF',
                        'target' => 'HIV/AIDS and NCD interventions strengthened',
                        'activity' => 'Implement workplace health programmes',
                        'output_indicator' => 'Interventions delivered',
                        'baseline' => 6,
                        'annual_target' => 8,
                        'actual_value' => 2,
                        'planned_budget_tzs' => 180000000,
                        'disbursed_tzs' => 45000000,
                        'expenditure_tzs' => 41000000,
                        'key_activity' => 'No'
                    ]
                ]],
['MIT-PPP-PERF', '2026 - Q2', 'published', [
                    [
                        'objective_code' => 'Y',
                        'target' => 'Industrial sector contribution to GDP increased',
                        'activity' => 'Coordinate implementation of industrial development programmes',
                        'output_indicator' => 'Programmes coordinated',
                        'baseline' => 8,
                        'annual_target' => 12,
                        'actual_value' => 6,
                        'planned_budget_tzs' => 1800000000,
                        'disbursed_tzs' => 880000000,
                        'expenditure_tzs' => 842000000,
                        'key_activity' => 'Yes'
                    ],
                    [
                        'objective_code' => 'X',
                        'target' => 'Trade facilitation and market access improved',
                        'activity' => 'Coordinate trade negotiations and NTB elimination',
                        'output_indicator' => 'Negotiations supported',
                        'baseline' => 10,
                        'annual_target' => 16,
                        'actual_value' => 8,
                        'planned_budget_tzs' => 950000000,
                        'disbursed_tzs' => 470000000,
                        'expenditure_tzs' => 449000000,
                        'key_activity' => 'Yes'
                    ],
                    [
                        'objective_code' => 'AF',
                        'target' => 'HIV/AIDS and NCD interventions strengthened',
                        'activity' => 'Implement workplace health programmes',
                        'output_indicator' => 'Interventions delivered',
                        'baseline' => 6,
                        'annual_target' => 8,
                        'actual_value' => 4,
                        'planned_budget_tzs' => 180000000,
                        'disbursed_tzs' => 92000000,
                        'expenditure_tzs' => 86000000,
                        'key_activity' => 'No'
                    ]
                ]],
['MIT-DTD-PROM', '2026 - Q1', 'published', [
                    [
                        'mppd_meetings' => 1,
                        'business_conferences' => 3,
                        'licence_inspections' => 480,
                        'formalized_enterprises' => 1240,
                        'exhibitions_coordinated' => 2,
                        'digital_marketing_campaigns' => 4,
                        'market_intelligence_reports' => 6,
                        'commodity_price_datasets' => 28,
                        'remarks' => null
                    ]
                ]],
['MIT-DTD-PROM', '2026 - Q2', 'published', [
                    [
                        'mppd_meetings' => 2,
                        'business_conferences' => 4,
                        'licence_inspections' => 530,
                        'formalized_enterprises' => 1480,
                        'exhibitions_coordinated' => 3,
                        'digital_marketing_campaigns' => 5,
                        'market_intelligence_reports' => 8,
                        'commodity_price_datasets' => 33,
                        'remarks' => 'DITF 2026 held in July'
                    ]
                ]],
['MIT-DTI-TRADE', '2026 - Q1', 'published', [
                    [
                        'market' => 'World',
                        'exports_usd' => 1820.0,
                        'imports_usd' => 3410.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'European Union',
                        'exports_usd' => 402.0,
                        'imports_usd' => 512.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'EAC',
                        'exports_usd' => 486.0,
                        'imports_usd' => 298.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'SADC',
                        'exports_usd' => 655.0,
                        'imports_usd' => 742.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'Asia',
                        'exports_usd' => 598.0,
                        'imports_usd' => 1920.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'China',
                        'exports_usd' => 312.0,
                        'imports_usd' => 1120.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'India',
                        'exports_usd' => 168.0,
                        'imports_usd' => 486.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'Japan',
                        'exports_usd' => 44.0,
                        'imports_usd' => 96.0,
                        'remarks' => null
                    ]
                ]],
['MIT-DTI-TRADE', '2026 - Q2', 'published', [
                    [
                        'market' => 'World',
                        'exports_usd' => 1960.0,
                        'imports_usd' => 3520.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'European Union',
                        'exports_usd' => 438.0,
                        'imports_usd' => 498.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'EAC',
                        'exports_usd' => 524.0,
                        'imports_usd' => 312.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'SADC',
                        'exports_usd' => 701.0,
                        'imports_usd' => 768.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'Asia',
                        'exports_usd' => 642.0,
                        'imports_usd' => 1980.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'China',
                        'exports_usd' => 341.0,
                        'imports_usd' => 1160.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'India',
                        'exports_usd' => 182.0,
                        'imports_usd' => 502.0,
                        'remarks' => null
                    ],
                    [
                        'market' => 'Japan',
                        'exports_usd' => 49.0,
                        'imports_usd' => 101.0,
                        'remarks' => null
                    ]
                ]],
['MIT-DTI-ACCESS', '2026 - Q1', 'published', [
                    [
                        'negotiations_participated' => 7,
                        'committee_meetings' => 11,
                        'awareness_workshops' => 9,
                        'ntbs_addressed' => 14,
                        'trade_missions' => 4,
                        'jtc_jpc_meetings' => 3,
                        'agreements_concluded' => 2,
                        'ecommerce_initiatives' => 3,
                        'remarks' => null
                    ]
                ]],
['MIT-DTI-ACCESS', '2026 - Q2', 'published', [
                    [
                        'negotiations_participated' => 9,
                        'committee_meetings' => 13,
                        'awareness_workshops' => 12,
                        'ntbs_addressed' => 18,
                        'trade_missions' => 6,
                        'jtc_jpc_meetings' => 4,
                        'agreements_concluded' => 3,
                        'ecommerce_initiatives' => 4,
                        'remarks' => null
                    ]
                ]],
['MIT-SME-DEV', '2026 - Q1', 'published', [
                    [
                        'loan_scheme_awareness_campaigns' => 12,
                        'enterprises_formalized' => 890,
                        'clusters_supported' => 7,
                        'lga_premises_supported' => 9,
                        'smes_linked_to_markets' => 64,
                        'sensitization_meetings' => 18,
                        'remarks' => null
                    ]
                ]],
['MIT-SME-DEV', '2026 - Q2', 'published', [
                    [
                        'loan_scheme_awareness_campaigns' => 15,
                        'enterprises_formalized' => 1020,
                        'clusters_supported' => 9,
                        'lga_premises_supported' => 11,
                        'smes_linked_to_markets' => 78,
                        'sensitization_meetings' => 22,
                        'remarks' => null
                    ]
                ]],
['MIT-FAU-FIN', '2026 - Q1', 'published', [
                    [
                        'payrolls_processed' => 3,
                        'institutions_paid' => 12,
                        'audit_queries_answered' => 18,
                        'audit_queries_pending' => 5,
                        'financial_statements_prepared' => 1,
                        'financial_reports_submitted' => 4,
                        'voucher_lists_submitted' => 3,
                        'disbursements_tzs' => 42600000000,
                        'remarks' => null
                    ]
                ]],
['MIT-FAU-FIN', '2026 - Q2', 'published', [
                    [
                        'payrolls_processed' => 3,
                        'institutions_paid' => 12,
                        'audit_queries_answered' => 22,
                        'audit_queries_pending' => 3,
                        'financial_statements_prepared' => 1,
                        'financial_reports_submitted' => 4,
                        'voucher_lists_submitted' => 3,
                        'disbursements_tzs' => 45100000000,
                        'remarks' => null
                    ]
                ]],
['MIT-ICT-SYS', '2026 - Q1', 'published', [
                    [
                        'network_uptime_percent' => 97.8,
                        'systems_maintained' => 24,
                        'databases_maintained' => 6,
                        'ict_governance_initiatives' => 2,
                        'ict_trainings' => 4,
                        'staff_trained_ict' => 68,
                        'remarks' => null
                    ]
                ]],
['MIT-ICT-SYS', '2026 - Q2', 'published', [
                    [
                        'network_uptime_percent' => 98.6,
                        'systems_maintained' => 26,
                        'databases_maintained' => 7,
                        'ict_governance_initiatives' => 3,
                        'ict_trainings' => 5,
                        'staff_trained_ict' => 82,
                        'remarks' => null
                    ]
                ]],
['MIT-PMU-PROC', '2026 - Q1', 'published', [
                    [
                        'procurement_plan_items' => 34,
                        'tenders_completed' => 12,
                        'stocktaking_exercises' => 1,
                        'assets_verified' => 480,
                        'assets_disposed' => 26,
                        'tender_board_meetings' => 6,
                        'remarks' => null
                    ]
                ]],
['MIT-PMU-PROC', '2026 - Q2', 'published', [
                    [
                        'procurement_plan_items' => 41,
                        'tenders_completed' => 15,
                        'stocktaking_exercises' => 1,
                        'assets_verified' => 520,
                        'assets_disposed' => 31,
                        'tender_board_meetings' => 7,
                        'remarks' => null
                    ]
                ]],
['MIT-LSU-LEGAL', '2026 - Q1', 'published', [
                    [
                        'date_received' => '2026-01-14',
                        'matter_type' => 'Contract',
                        'requesting_unit' => 'Department of Industrial Development',
                        'description' => 'EPZ infrastructure development contract review',
                        'action_taken' => 'Draft reviewed and comments issued',
                        'compliant' => 'Yes',
                        'status' => 'Closed'
                    ],
                    [
                        'date_received' => '2026-02-03',
                        'matter_type' => 'MoU',
                        'requesting_unit' => 'Department of Trade Integration',
                        'description' => 'MoU with EAC Secretariat on NTB reporting',
                        'action_taken' => 'Negotiated and cleared for signature',
                        'compliant' => 'Yes',
                        'status' => 'Closed'
                    ],
                    [
                        'date_received' => '2026-02-20',
                        'matter_type' => 'Dispute',
                        'requesting_unit' => 'Finance and Accounting Unit',
                        'description' => 'Supplier payment dispute — office equipment',
                        'action_taken' => 'Representing the Ministry',
                        'compliant' => 'Partly',
                        'status' => 'Open'
                    ],
                    [
                        'date_received' => '2026-03-09',
                        'matter_type' => 'Advice',
                        'requesting_unit' => 'Department of Policy and Planning',
                        'description' => 'Interpretation of Trade Remedies Act provisions',
                        'action_taken' => 'Legal opinion issued',
                        'compliant' => 'Yes',
                        'status' => 'Closed'
                    ]
                ]],
['MIT-LSU-LEGAL', '2026 - Q2', 'published', [
                    [
                        'date_received' => '2026-04-15',
                        'matter_type' => 'Guarantee',
                        'requesting_unit' => 'Department of Industrial Development',
                        'description' => 'Guarantee for KMTC machinery procurement',
                        'action_taken' => 'Vetting in progress',
                        'compliant' => 'Yes',
                        'status' => 'Open'
                    ],
                    [
                        'date_received' => '2026-05-06',
                        'matter_type' => 'Consultancy',
                        'requesting_unit' => 'ICT Unit',
                        'description' => 'Data warehouse consultancy agreement',
                        'action_taken' => 'Contract executed and registered',
                        'compliant' => 'Yes',
                        'status' => 'Closed'
                    ],
                    [
                        'date_received' => '2026-06-11',
                        'matter_type' => 'Opinion',
                        'requesting_unit' => 'Procurement Management Unit',
                        'description' => 'PPA compliance opinion on framework contracts',
                        'action_taken' => 'Opinion issued',
                        'compliant' => 'Yes',
                        'status' => 'Closed'
                    ]
                ]],
['MIT-LSU-CONT', '2026 - Q2', 'published', [
                    [
                        'title' => 'KMTC machinery supply contract',
                        'parties' => 'MIT / NDC / Supplier consortium',
                        'start_date' => '2025-07-01',
                        'expiry_date' => '2026-11-30',
                        'value_tzs' => 8400000000,
                        'registered' => 'Yes',
                        'compliance_status' => 'Compliant'
                    ],
                    [
                        'title' => 'Data warehouse consultancy MoU',
                        'parties' => 'MIT / ICT consultant',
                        'start_date' => '2026-04-01',
                        'expiry_date' => '2026-12-15',
                        'value_tzs' => 640000000,
                        'registered' => 'Yes',
                        'compliance_status' => 'Compliant'
                    ],
                    [
                        'title' => 'DITF grounds lease',
                        'parties' => 'MIT / TANTRADE',
                        'start_date' => '2024-01-01',
                        'expiry_date' => '2027-06-30',
                        'value_tzs' => 1200000000,
                        'registered' => 'Yes',
                        'compliance_status' => 'Compliant'
                    ],
                    [
                        'title' => 'Office equipment framework agreement',
                        'parties' => 'MIT / PMU suppliers',
                        'start_date' => '2025-10-01',
                        'expiry_date' => '2026-10-31',
                        'value_tzs' => 380000000,
                        'registered' => 'No',
                        'compliance_status' => 'Attention needed'
                    ]
                ]],
['MIT-ME-PERF', '2026 - Q1', 'published', [
                    [
                        'nkra_reports' => 1,
                        'evaluations_conducted' => 2,
                        'impact_assessments' => 1,
                        'institutions_reviewed' => 6,
                        'manifesto_directives_tracked' => 24,
                        'recommendations_followed_up' => 31,
                        'remarks' => null
                    ]
                ]],
['MIT-ME-PERF', '2026 - Q2', 'published', [
                    [
                        'nkra_reports' => 1,
                        'evaluations_conducted' => 3,
                        'impact_assessments' => 2,
                        'institutions_reviewed' => 8,
                        'manifesto_directives_tracked' => 28,
                        'recommendations_followed_up' => 37,
                        'remarks' => null
                    ]
                ]],
['MIT-IAU-AUDIT', '2026 - Q1', 'published', [
                    [
                        'audits_planned' => 8,
                        'audits_conducted' => 6,
                        'findings_issued' => 34,
                        'findings_resolved' => 21,
                        'performance_audits' => 2,
                        'project_audits' => 3,
                        'audit_committee_meetings' => 1,
                        'quarterly_reports' => 1,
                        'remarks' => null
                    ]
                ]],
['MIT-IAU-AUDIT', '2026 - Q2', 'published', [
                    [
                        'audits_planned' => 9,
                        'audits_conducted' => 8,
                        'findings_issued' => 29,
                        'findings_resolved' => 26,
                        'performance_audits' => 3,
                        'project_audits' => 4,
                        'audit_committee_meetings' => 1,
                        'quarterly_reports' => 1,
                        'remarks' => null
                    ]
                ]],
['MIT-GCU-COMM', '2026 - Q1', 'published', [
                    [
                        'press_briefings' => 6,
                        'media_engagements' => 14,
                        'articles_published' => 9,
                        'awareness_campaigns' => 5,
                        'website_updates' => 68,
                        'remarks' => null
                    ]
                ]],
['MIT-GCU-COMM', '2026 - Q2', 'published', [
                    [
                        'press_briefings' => 8,
                        'media_engagements' => 17,
                        'articles_published' => 12,
                        'awareness_campaigns' => 7,
                        'website_updates' => 81,
                        'remarks' => null
                    ]
                ]],
['MIT-HR-SDW', '2026 - Q1', 'published', [
                    [
                        'staff_trained_short' => 46,
                        'staff_trained_long' => 7,
                        'statutory_meetings_supported' => 9,
                        'professional_workshops_supported' => 12,
                        'hiv_ncd_interventions' => 3,
                        'anticorruption_programmes' => 2,
                        'staff_welfare_interventions' => 15,
                        'remarks' => null
                    ]
                ]],
['MIT-HR-SDW', '2026 - Q2', 'published', [
                    [
                        'staff_trained_short' => 58,
                        'staff_trained_long' => 9,
                        'statutory_meetings_supported' => 11,
                        'professional_workshops_supported' => 15,
                        'hiv_ncd_interventions' => 4,
                        'anticorruption_programmes' => 3,
                        'staff_welfare_interventions' => 18,
                        'remarks' => null
                    ]
                ]],
        ];
    }
}
