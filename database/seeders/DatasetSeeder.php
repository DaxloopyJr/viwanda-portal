<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        $datasets = [
            [
                'code' => 'FCT-AC',
                'name' => 'Appeal/Application Case',
                'description' => 'Determination of Appeal/Application cases involving competition and regulatory issues arising from orders and decisions of the Fair Competition Commission (FCC) and regulatory authorities (EWURA, TCRA, TCAA, LATRA, PURA).',
                'institution' => 'FCT',
                'frequency' => 'Quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'case_no', 'label' => 'Case No.', 'type' => 'string', 'required' => true],
                    ['name' => 'case_originated', 'label' => 'Case Originated', 'type' => 'string', 'required' => true,
                        'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'case_sector', 'label' => 'Case Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'case_status', 'label' => 'Status', 'type' => 'string', 'required' => true,
                        'options' => ['Application withdrawn', 'Appeal withdrawn', 'Order delivered', 'Pending hearing', 'Dismissed', 'Upheld']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCC-CN',
                'name' => 'Competition Complaints and Notifications',
                'description' => 'Complaints lodged and merger/acquisition notifications received by the Fair Competition Commission.',
                'institution' => 'FCC',
                'frequency' => 'Quarterly',
                'priority' => 'high',
                'source_system' => 'FCC Case Management System',
                'consumers' => 'MIT, FCT, Public',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date Received', 'type' => 'date', 'required' => true],
                    ['name' => 'reference_no', 'label' => 'Reference No.', 'type' => 'string', 'required' => true],
                    ['name' => 'matter_type', 'label' => 'Matter Type', 'type' => 'string', 'required' => true,
                        'options' => ['Complaint', 'Merger notification', 'Exemption application', 'Investigation']],
                    ['name' => 'sector', 'label' => 'Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'matter_status', 'label' => 'Status', 'type' => 'string', 'required' => true,
                        'options' => ['Under investigation', 'Determined', 'Withdrawn', 'Referred to FCT']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'SIDO-IE',
                'name' => 'Industrial Establishments and Employment',
                'description' => 'Number of registered small industrial establishments and employment by region and subsector.',
                'institution' => 'SIDO',
                'frequency' => 'Quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, NBS',
                'fields' => [
                    ['name' => 'region', 'label' => 'Region', 'type' => 'string', 'required' => true],
                    ['name' => 'district', 'label' => 'District', 'type' => 'string', 'required' => true],
                    ['name' => 'subsector', 'label' => 'Subsector', 'type' => 'string', 'required' => true,
                        'options' => ['Agro-processing', 'Textiles', 'Wood products', 'Metal works', 'Leather', 'Food processing', 'Other']],
                    ['name' => 'establishments', 'label' => 'Establishments', 'type' => 'integer', 'required' => true],
                    ['name' => 'employment', 'label' => 'Employment', 'type' => 'integer', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-PC',
                'name' => 'Product Certifications',
                'description' => 'Product certifications issued under the TBS Standards Mark scheme.',
                'institution' => 'TBS',
                'frequency' => 'Quarterly',
                'priority' => 'medium',
                'source_system' => 'TBS Certification System',
                'consumers' => 'MIT, Public',
                'fields' => [
                    ['name' => 'date', 'label' => 'Certification Date', 'type' => 'date', 'required' => true],
                    ['name' => 'product', 'label' => 'Product', 'type' => 'string', 'required' => true],
                    ['name' => 'company', 'label' => 'Company', 'type' => 'string', 'required' => true],
                    ['name' => 'standard', 'label' => 'Standard', 'type' => 'string', 'required' => true],
                    ['name' => 'certificate_status', 'label' => 'Certificate Status', 'type' => 'string', 'required' => true,
                        'options' => ['Issued', 'Renewed', 'Suspended', 'Cancelled']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
        ];

        foreach ($datasets as $data) {
            $institution = Institution::where('code', $data['institution'])->firstOrFail();
            unset($data['institution']);
            Dataset::firstOrCreate(['code' => $data['code']], $data + ['institution_id' => $institution->id]);
        }
    }
}
