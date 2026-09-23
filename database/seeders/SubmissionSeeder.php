<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\Institution;
use App\Models\Submission;
use App\Models\SubmissionRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $fct = Institution::where('code', 'FCT')->firstOrFail();
        $officer = User::where('email', 'fct.officer@fct.go.tz')->firstOrFail();
        $reviewer = User::where('email', 'reviewer@viwanda.go.tz')->firstOrFail();
        $dataset = Dataset::where('code', 'FCT-AC')->firstOrFail();

        // Real sample rows from the FCT data requirements form.
        $fctSubmission = Submission::firstOrCreate(
            ['reference' => 'VP-2021-00001'],
            [
                'transaction_reference' => 'FCT-2021-Q3-001',
                'institution_id' => $fct->id,
                'dataset_id' => $dataset->id,
                'reporting_period' => '2021 - Q3',
                'channel' => 'portal',
                'consumers' => ['TR', 'MIT', 'Public'],
                'status' => 'published',
                'submitted_by' => $officer->id,
                'submitted_at' => '2021-10-05 09:30:00',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => '2021-10-07 14:10:00',
                'published_at' => '2021-10-08 08:00:00',
                'records_count' => 4,
            ]
        );

        if ($fctSubmission->records()->count() === 0) {
            $rows = [
                ['date' => '2021-08-03', 'case_no' => 'Application No.28/2020', 'case_originated' => 'TCRA',
                    'case_sector' => 'Communications', 'case_status' => 'Application withdrawn',
                    'remarks' => 'Withdrawn as prayed by Applicant following ongoing negotiations to settle the compliance order (made by TCRA) between the parties.'],
                ['date' => '2021-08-05', 'case_no' => 'Appeal No. 24/2020', 'case_originated' => 'FCC',
                    'case_sector' => 'Competition: Mergers and Acquisitions', 'case_status' => 'Appeal withdrawn', 'remarks' => null],
                ['date' => '2021-08-12', 'case_no' => 'Appeal No. 16/2020', 'case_originated' => 'EWURA',
                    'case_sector' => 'Energy: Electrical fault', 'case_status' => 'Order delivered', 'remarks' => null],
                ['date' => '2021-08-19', 'case_no' => 'Appeal No. 15/2020', 'case_originated' => 'EWURA',
                    'case_sector' => 'Energy: Petroleum sector', 'case_status' => 'Order delivered', 'remarks' => null],
            ];
            foreach ($rows as $i => $row) {
                SubmissionRecord::create([
                    'submission_id' => $fctSubmission->id,
                    'row_number' => $i + 1,
                    'data' => $row,
                ]);
            }
        }

        // Demo submissions across institutions/periods/statuses so dashboards are meaningful.
        $fakerRows = [
            'FCT-AC' => [
                ['date' => '2026-01-22', 'case_no' => 'Appeal No. 03/2026', 'case_originated' => 'FCC', 'case_sector' => 'Competition: Abuse of dominance', 'case_status' => 'Pending hearing', 'remarks' => null],
                ['date' => '2026-02-09', 'case_no' => 'Application No. 05/2026', 'case_originated' => 'LATRA', 'case_sector' => 'Transport: Fare dispute', 'case_status' => 'Order delivered', 'remarks' => null],
                ['date' => '2026-03-17', 'case_no' => 'Appeal No. 09/2026', 'case_originated' => 'PURA', 'case_sector' => 'Petroleum upstream licensing', 'case_status' => 'Upheld', 'remarks' => 'Decision of the authority upheld with costs'],
            ],
            'FCC-CN' => [
                ['date' => '2026-01-15', 'reference_no' => 'FCC/CMP/2026/011', 'matter_type' => 'Complaint', 'sector' => 'Manufacturing', 'matter_status' => 'Under investigation', 'remarks' => null],
                ['date' => '2026-02-02', 'reference_no' => 'FCC/MRG/2026/004', 'matter_type' => 'Merger notification', 'sector' => 'Beverages', 'matter_status' => 'Determined', 'remarks' => 'Approved with conditions'],
                ['date' => '2026-03-11', 'reference_no' => 'FCC/EXM/2026/002', 'matter_type' => 'Exemption application', 'sector' => 'Transport', 'matter_status' => 'Determined', 'remarks' => null],
            ],
            'SIDO-IE' => [
                ['region' => 'Dar es Salaam', 'district' => 'Ilala', 'subsector' => 'Agro-processing', 'establishments' => 145, 'employment' => 2130, 'remarks' => null],
                ['region' => 'Arusha', 'district' => 'Arusha City', 'subsector' => 'Textiles', 'establishments' => 62, 'employment' => 890, 'remarks' => null],
                ['region' => 'Mwanza', 'district' => 'Ilemela', 'subsector' => 'Food processing', 'establishments' => 88, 'employment' => 1204, 'remarks' => null],
            ],
            'TBS-PCI' => [
                ['date' => '2026-01-20', 'company' => 'Kilimanjaro Water Ltd', 'product' => 'Bottled drinking water', 'certificate_no' => 'TZS/SM/2026/0101', 'client_category' => 'Large enterprise', 'status' => 'Issued'],
                ['date' => '2026-02-14', 'company' => 'Azam Millers', 'product' => 'Wheat flour', 'certificate_no' => 'TZS/SM/2026/0158', 'client_category' => 'SME', 'status' => 'Renewed'],
                ['date' => '2026-03-03', 'company' => 'Mbinga Coffee Cooperative', 'product' => 'Roasted coffee', 'certificate_no' => 'TZS/SM/2026/0204', 'client_category' => 'Medium enterprise', 'status' => 'Issued'],
            ],
        ];

        $plan = [
            ['dataset' => 'FCC-CN', 'period' => '2026 - Q1', 'channel' => 'api', 'status' => 'published'],
            ['dataset' => 'FCC-CN', 'period' => '2026 - Q2', 'channel' => 'api', 'status' => 'under_review'],
            ['dataset' => 'SIDO-IE', 'period' => '2026 - Q1', 'channel' => 'upload', 'status' => 'accepted'],
            ['dataset' => 'SIDO-IE', 'period' => '2026 - Q2', 'channel' => 'portal', 'status' => 'submitted'],
            ['dataset' => 'TBS-PCI', 'period' => '2026 - Q1', 'channel' => 'api', 'status' => 'published'],
            ['dataset' => 'TBS-PCI', 'period' => '2026 - Q2', 'channel' => 'api', 'status' => 'returned'],
            ['dataset' => 'FCT-AC', 'period' => '2026 - Q1', 'channel' => 'portal', 'status' => 'accepted'],
            ['dataset' => 'FCT-AC', 'period' => '2026 - Q2', 'channel' => 'portal', 'status' => 'draft'],
        ];

        foreach ($plan as $i => $item) {
            $ds = Dataset::where('code', $item['dataset'])->firstOrFail();
            $status = $item['status'];
            $submission = Submission::firstOrCreate(
                ['reference' => sprintf('VP-2026-%05d', $i + 2)],
                [
                    'transaction_reference' => $item['dataset'].'-'.$item['period'].'-001',
                    'institution_id' => $ds->institution_id,
                    'dataset_id' => $ds->id,
                    'reporting_period' => $item['period'],
                    'channel' => $item['channel'],
                    'status' => $status,
                    'submitted_by' => $officer->id,
                    'submitted_at' => $status === 'draft' ? null : now()->subDays(20 - $i),
                    'reviewed_by' => in_array($status, ['under_review', 'returned', 'accepted', 'rejected', 'published'], true) ? $reviewer->id : null,
                    'reviewed_at' => in_array($status, ['returned', 'accepted', 'rejected', 'published'], true) ? now()->subDays(10 - $i) : null,
                    'review_comments' => $status === 'returned' ? 'Row 3: certificate status requires a supporting remark.' : null,
                    'published_at' => $status === 'published' ? now()->subDays(5 - $i) : null,
                    'records_count' => count($fakerRows[$item['dataset']]),
                ]
            );

            if ($submission->records()->count() === 0) {
                foreach ($fakerRows[$item['dataset']] as $n => $row) {
                    SubmissionRecord::create([
                        'submission_id' => $submission->id,
                        'row_number' => $n + 1,
                        'data' => $row,
                    ]);
                }
            }
        }
    }
}
