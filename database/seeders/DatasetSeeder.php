<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\Department;
use App\Models\Institution;
use Illuminate\Database\Seeder;

/**
 * Data catalogue built from the official data-requirements documents:
 *  - FCT "Data Requirements for Developing Data Warehouse" (14 datasets)
 *  - TBS "Data Requirements for Developing Data Warehouse" (19 datasets)
 * plus the FCC and SIDO catalogue entries.
 */
class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        $datasets = [

            [
                'code' => 'FCC-CN',
                'name' => 'Competition Complaints and Notifications',
                'description' => 'Complaints lodged and merger/acquisition notifications received by the Fair Competition Commission.',
                'institution' => 'FCC',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => 'FCC Case Management System',
                'consumers' => 'MIT, FCT, Public',
                'department' => null,
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
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, NBS',
                'department' => null,
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
                'code' => 'FCT-AC',
                'name' => 'Appeal/Application Case',
                'description' => 'Determination of Appeal/Application cases involving competition and regulatory issues arising from orders and decisions of the Fair Competition Commission (FCC) and regulatory authorities (EWURA, TCRA, TCAA, LATRA, PURA).',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date Registered', 'type' => 'date', 'required' => true],
                    ['name' => 'case_no', 'label' => 'Case Number', 'type' => 'string', 'required' => true],
                    ['name' => 'case_originated', 'label' => 'Authority', 'type' => 'string', 'required' => true, 'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'case_sector', 'label' => 'Subject Matter', 'type' => 'string', 'required' => true],
                    ['name' => 'case_status', 'label' => 'Determination', 'type' => 'string', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-CA',
                'name' => 'Cases Adjudicated',
                'description' => 'Number and details of cases determined, dismissed or upheld by the Tribunal within the reporting period.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date_determined', 'label' => 'Date Determined', 'type' => 'date', 'required' => true],
                    ['name' => 'case_no', 'label' => 'Case Number', 'type' => 'string', 'required' => true],
                    ['name' => 'authority', 'label' => 'Authority', 'type' => 'string', 'required' => true, 'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'subject_matter', 'label' => 'Subject Matter', 'type' => 'string', 'required' => true],
                    ['name' => 'determination', 'label' => 'Determination', 'type' => 'string', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-PN',
                'name' => 'Public Notices of Anticipated Cases Prepared and Published',
                'description' => 'Notices of anticipated cases prepared and published through media to inform stakeholders and the public of upcoming cases.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date_published', 'label' => 'Date Published', 'type' => 'date', 'required' => true],
                    ['name' => 'case_no', 'label' => 'Case Number', 'type' => 'string', 'required' => true],
                    ['name' => 'authority', 'label' => 'Authority', 'type' => 'string', 'required' => true, 'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'notice_title', 'label' => 'Notice Title', 'type' => 'string', 'required' => true],
                    ['name' => 'publication_media', 'label' => 'Publication Media', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Published', 'Pending', 'Draft']],
                ],
            ],
            [
                'code' => 'FCT-RC',
                'name' => 'Resolved Cases against Cases Due for Hearing',
                'description' => 'Comparison of cases resolved versus cases pending and due for hearing within the quarter.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'authority', 'label' => 'Authority', 'type' => 'string', 'required' => true, 'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'cases_resolved', 'label' => 'Cases Resolved', 'type' => 'integer', 'required' => true],
                    ['name' => 'cases_due', 'label' => 'Cases Due for Hearing', 'type' => 'integer', 'required' => true],
                    ['name' => 'resolution_rate', 'label' => 'Resolution Rate (%)', 'type' => 'number', 'required' => false],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-ATD',
                'name' => 'Average Time Taken to Dispose of Cases (Months)',
                'description' => 'Average number of months taken by the Tribunal to dispose of appeal/application cases from filing to determination.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'authority', 'label' => 'Authority', 'type' => 'string', 'required' => true, 'options' => ['FCC', 'TCRA', 'EWURA', 'TCAA', 'LATRA', 'PURA']],
                    ['name' => 'cases_disposed', 'label' => 'Cases Disposed', 'type' => 'integer', 'required' => true],
                    ['name' => 'total_months', 'label' => 'Total Months Taken', 'type' => 'number', 'required' => true],
                    ['name' => 'average_months', 'label' => 'Average Months', 'type' => 'number', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-OE',
                'name' => 'Operational Efficiency of the Tribunal Appeal Case Management System (Digital Transmission)',
                'description' => 'Performance of the digital case management system measured by digital transmission rate, system uptime and processing efficiency.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'cases_filed', 'label' => 'Cases Filed', 'type' => 'integer', 'required' => true],
                    ['name' => 'cases_digital', 'label' => 'Cases Transmitted Digitally', 'type' => 'integer', 'required' => true],
                    ['name' => 'system_uptime', 'label' => 'System Uptime (%)', 'type' => 'number', 'required' => false],
                    ['name' => 'efficiency', 'label' => 'Efficiency (%)', 'type' => 'number', 'required' => false],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-RU',
                'name' => 'Tribunal Registry Upgraded',
                'description' => 'Registry upgrade activities including hardware, software, digitization and records management improvements.',
                'institution' => 'FCT',
                'frequency' => 'annually',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'upgrade_component', 'label' => 'Upgrade Component', 'type' => 'string', 'required' => true, 'options' => ['Hardware', 'Software', 'Digitization', 'Records Management']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'text', 'required' => true],
                    ['name' => 'acquisition', 'label' => 'Acquisition', 'type' => 'string', 'required' => false],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Completed', 'Ongoing', 'Planned']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-TRR',
                'name' => 'Tribunal Rules Reviewed',
                'description' => 'Tribunal rules reviewed/amended to improve case handling and align with legal and regulatory developments.',
                'institution' => 'FCT',
                'frequency' => 'annually',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'rule_provision', 'label' => 'Rule/Provision Reviewed', 'type' => 'string', 'required' => true],
                    ['name' => 'review_aspect', 'label' => 'Review Aspect', 'type' => 'string', 'required' => true],
                    ['name' => 'responsible_team', 'label' => 'Responsible Team', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Completed', 'Ongoing', 'Planned']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-TRP',
                'name' => 'Tribunal Rules Published',
                'description' => 'Reviewed Tribunal rules published in the Government Gazette and other media for public awareness and enforcement.',
                'institution' => 'FCT',
                'frequency' => 'annually',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date_published', 'label' => 'Date Published', 'type' => 'date', 'required' => true],
                    ['name' => 'gazette_no', 'label' => 'Gazette No.', 'type' => 'string', 'required' => true],
                    ['name' => 'rule_title', 'label' => 'Rule Title', 'type' => 'string', 'required' => true],
                    ['name' => 'effective_date', 'label' => 'Effective Date', 'type' => 'date', 'required' => false],
                    ['name' => 'publication_media', 'label' => 'Publication Media', 'type' => 'string', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-ES',
                'name' => 'Economic Studies on Competition and Regulated Sectors Prepared',
                'description' => 'Economic studies/research prepared on competition and regulated sectors to support adjudication and policy advice.',
                'institution' => 'FCT',
                'frequency' => 'annually',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Economic Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'study_title', 'label' => 'Study Title', 'type' => 'string', 'required' => true],
                    ['name' => 'sector_covered', 'label' => 'Sector Covered', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Completed', 'Ongoing', 'Draft']],
                    ['name' => 'author_team', 'label' => 'Author/Team', 'type' => 'string', 'required' => true],
                    ['name' => 'dissemination_channel', 'label' => 'Dissemination Channel', 'type' => 'string', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-CR',
                'name' => 'Complaints Resolved within the Established Timeframe',
                'description' => 'Number and percentage of complaints resolved within the timeframe established by the Tribunal\'s service standards.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Finance and Administration Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'complaints_received', 'label' => 'Complaints Received', 'type' => 'integer', 'required' => true],
                    ['name' => 'complaints_resolved', 'label' => 'Complaints Resolved', 'type' => 'integer', 'required' => true],
                    ['name' => 'resolved_within_timeframe', 'label' => 'Resolved within Timeframe', 'type' => 'integer', 'required' => true],
                    ['name' => 'compliance_rate', 'label' => 'Compliance Rate (%)', 'type' => 'number', 'required' => false],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-ART',
                'name' => 'Average Response Time in Serving Clients (Days)',
                'description' => 'Average number of days taken to respond to and serve clients on enquiries, applications and case correspondence.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Finance and Administration Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'enquiries_received', 'label' => 'Enquiries/Requests Received', 'type' => 'integer', 'required' => true],
                    ['name' => 'total_response_days', 'label' => 'Total Response Days', 'type' => 'integer', 'required' => true],
                    ['name' => 'average_response_days', 'label' => 'Average Response Days', 'type' => 'number', 'required' => true],
                    ['name' => 'standard_days', 'label' => 'Standard (Days)', 'type' => 'integer', 'required' => true],
                    ['name' => 'compliance', 'label' => 'Compliance', 'type' => 'string', 'required' => true],
                ],
            ],
            [
                'code' => 'FCT-CM',
                'name' => 'Consultative Meetings Held with Stakeholders',
                'description' => 'Consultative meetings held with stakeholders including regulators, government institutions, private sector and professional bodies.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Legal Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'meeting_title', 'label' => 'Meeting Title', 'type' => 'string', 'required' => true],
                    ['name' => 'stakeholder_category', 'label' => 'Stakeholder Category', 'type' => 'string', 'required' => true],
                    ['name' => 'venue', 'label' => 'Venue', 'type' => 'string', 'required' => true],
                    ['name' => 'participants', 'label' => 'No. of Participants', 'type' => 'integer', 'required' => true],
                    ['name' => 'key_output', 'label' => 'Key Output/Recommendation', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'FCT-PA',
                'name' => 'Public Awareness Conducted',
                'description' => 'Public awareness activities conducted to educate stakeholders and the public on competition and regulatory matters and Tribunal processes.',
                'institution' => 'FCT',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'TR, MIT, Public',
                'department' => 'Economic Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'activity_name', 'label' => 'Activity/Event Name', 'type' => 'string', 'required' => true],
                    ['name' => 'venue_region', 'label' => 'Venue/Region', 'type' => 'string', 'required' => true],
                    ['name' => 'target_audience', 'label' => 'Target Audience', 'type' => 'string', 'required' => true],
                    ['name' => 'participants', 'label' => 'No. of Participants', 'type' => 'integer', 'required' => true],
                    ['name' => 'medium', 'label' => 'Medium', 'type' => 'string', 'required' => true],
                    ['name' => 'key_message', 'label' => 'Key Message', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-NSF',
                'name' => 'National Standards Formulated',
                'description' => 'National standards formulated and approved in different sectors of the economy through technical committee processes.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public, Industry',
                'department' => 'Standards Development Department',
                'fields' => [
                    ['name' => 'date_approved', 'label' => 'Date Approved', 'type' => 'date', 'required' => true],
                    ['name' => 'standard_no', 'label' => 'Standard No.', 'type' => 'string', 'required' => true],
                    ['name' => 'standard_title', 'label' => 'Standard Title', 'type' => 'string', 'required' => true],
                    ['name' => 'sector', 'label' => 'Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'stage', 'label' => 'Stage', 'type' => 'string', 'required' => false, 'options' => ['Proposal', 'Draft', 'Public Review', 'Approved']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Approved', 'Gazetted', 'Under development']],
                ],
            ],
            [
                'code' => 'TBS-SS',
                'name' => 'Standards Sold',
                'description' => 'Sales of national and international standards to clients through the standards sales point and online channels.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Public, Industry',
                'department' => 'Standards Information and Documentation Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'standard_no', 'label' => 'Standard No.', 'type' => 'string', 'required' => true],
                    ['name' => 'standard_title', 'label' => 'Standard Title', 'type' => 'string', 'required' => true],
                    ['name' => 'client_category', 'label' => 'Client Category', 'type' => 'string', 'required' => true, 'options' => ['Government', 'Industry', 'SME', 'Individual', 'Academic']],
                    ['name' => 'copies_sold', 'label' => 'Copies Sold', 'type' => 'integer', 'required' => true],
                    ['name' => 'amount_tzs', 'label' => 'Amount (TZS)', 'type' => 'number', 'required' => true],
                ],
            ],
            [
                'code' => 'TBS-QCT',
                'name' => 'SQMT Quality Control Trainings Conducted',
                'description' => 'Quality control trainings conducted on Standardization, Quality Assurance, Metrology and Testing (SQMT) for industry and stakeholders.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, SMEs',
                'department' => 'Training and Capacity Building Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'training_title', 'label' => 'Training Title', 'type' => 'string', 'required' => true],
                    ['name' => 'sqmt_area', 'label' => 'SQMT Area', 'type' => 'string', 'required' => true, 'options' => ['Standardization', 'Quality Assurance', 'Metrology', 'Testing']],
                    ['name' => 'venue_region', 'label' => 'Venue/Region', 'type' => 'string', 'required' => true],
                    ['name' => 'participants', 'label' => 'No. of Participants', 'type' => 'integer', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-SE',
                'name' => 'Standards Enquiries Responded',
                'description' => 'Responses provided to all enquiries on standards and standards-related information received from stakeholders and the public.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public, Industry',
                'department' => 'Standards Information and Documentation Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'enquiries_received', 'label' => 'Enquiries Received', 'type' => 'integer', 'required' => true],
                    ['name' => 'enquiries_responded', 'label' => 'Enquiries Responded', 'type' => 'integer', 'required' => true],
                    ['name' => 'average_response_days', 'label' => 'Average Response Days', 'type' => 'number', 'required' => false],
                    ['name' => 'channel', 'label' => 'Channel', 'type' => 'string', 'required' => false, 'options' => ['Email', 'Phone', 'Walk-in', 'Letter', 'Online portal']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-NT',
                'name' => 'Notifications on Standards and Technical Regulations Issued',
                'description' => 'Notifications issued on standards, technical regulations and related issues, including notifications to the WTO TBT/SPS enquiry points.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, WTO, Public',
                'department' => 'Standards Development Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'notification_no', 'label' => 'Notification No.', 'type' => 'string', 'required' => true],
                    ['name' => 'subject', 'label' => 'Subject', 'type' => 'string', 'required' => true],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'string', 'required' => true, 'options' => ['Standard', 'Technical Regulation', 'Conformity Assessment', 'SPS', 'TBT']],
                    ['name' => 'notified_to', 'label' => 'Notified To', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Pending', 'Acknowledged']],
                ],
            ],
            [
                'code' => 'TBS-SRP',
                'name' => 'SQMT Research Programmes Conducted',
                'description' => 'Research programmes conducted on Standardization, Quality Assurance, Metrology and Testing (SQMT) to support standards development and service improvement.',
                'institution' => 'TBS',
                'frequency' => 'annually',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Research and Development Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'research_title', 'label' => 'Research Title', 'type' => 'string', 'required' => true],
                    ['name' => 'sqmt_area', 'label' => 'SQMT Area', 'type' => 'string', 'required' => true, 'options' => ['Standardization', 'Quality Assurance', 'Metrology', 'Testing']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Completed', 'Ongoing', 'Planned']],
                    ['name' => 'lead_researcher', 'label' => 'Lead Researcher/Team', 'type' => 'string', 'required' => true],
                    ['name' => 'dissemination_channel', 'label' => 'Dissemination Channel', 'type' => 'string', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-PST',
                'name' => 'Product Samples Tested',
                'description' => 'Tests performed on samples of different products against relevant standards and specifications.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, Regulators',
                'department' => 'Testing Department (Laboratories)',
                'fields' => [
                    ['name' => 'date_received', 'label' => 'Date Received', 'type' => 'date', 'required' => true],
                    ['name' => 'sample_product', 'label' => 'Sample/Product', 'type' => 'string', 'required' => true],
                    ['name' => 'test_type', 'label' => 'Test Type', 'type' => 'string', 'required' => true],
                    ['name' => 'client', 'label' => 'Client', 'type' => 'string', 'required' => true],
                    ['name' => 'test_result', 'label' => 'Test Result', 'type' => 'string', 'required' => true, 'options' => ['Passed', 'Failed', 'Inconclusive']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-EC',
                'name' => 'Equipment/Items Calibrated',
                'description' => 'Calibrations performed on different equipment and items to ensure measurement traceability.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, Public',
                'department' => 'Metrology Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'equipment_item', 'label' => 'Equipment/Item', 'type' => 'string', 'required' => true],
                    ['name' => 'client', 'label' => 'Client', 'type' => 'string', 'required' => true],
                    ['name' => 'certificate_no', 'label' => 'Calibration Certificate No.', 'type' => 'string', 'required' => true],
                    ['name' => 'next_due_date', 'label' => 'Next Due Date', 'type' => 'date', 'required' => false],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-PCI',
                'name' => 'Product Certification Issued',
                'description' => 'Product certifications issued for products from various sectors, including SMEs\' products, under the Standards Mark scheme.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, SMEs',
                'department' => 'Certification Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'company', 'label' => 'Company', 'type' => 'string', 'required' => true],
                    ['name' => 'product', 'label' => 'Product', 'type' => 'string', 'required' => true],
                    ['name' => 'certificate_no', 'label' => 'Certificate/Licence No.', 'type' => 'string', 'required' => true],
                    ['name' => 'client_category', 'label' => 'Client Category', 'type' => 'string', 'required' => true, 'options' => ['Large enterprise', 'SME', 'Individual']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Renewed', 'Suspended', 'Cancelled']],
                ],
            ],
            [
                'code' => 'TBS-FCR',
                'name' => 'Food and Cosmetic Products Registration Permits Issued',
                'description' => 'Registration permits issued for food and cosmetic products placed on the market.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, Public',
                'department' => 'Food and Cosmetics Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'applicant', 'label' => 'Applicant', 'type' => 'string', 'required' => true],
                    ['name' => 'product_name', 'label' => 'Product Name', 'type' => 'string', 'required' => true],
                    ['name' => 'category', 'label' => 'Category (Food/Cosmetic)', 'type' => 'string', 'required' => true, 'options' => ['Food', 'Cosmetic']],
                    ['name' => 'permit_no', 'label' => 'Permit No.', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Renewed', 'Suspended', 'Cancelled']],
                ],
            ],
            [
                'code' => 'TBS-PRP',
                'name' => 'Premises Registration Permits Issued',
                'description' => 'Registration permits issued for premises manufacturing food and cosmetic products.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry, Public',
                'department' => 'Food and Cosmetics Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'premises_name', 'label' => 'Premises Name', 'type' => 'string', 'required' => true],
                    ['name' => 'owner', 'label' => 'Owner', 'type' => 'string', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'string', 'required' => true],
                    ['name' => 'permit_no', 'label' => 'Permit No.', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Renewed', 'Suspended', 'Cancelled']],
                ],
            ],
            [
                'code' => 'TBS-FRA',
                'name' => 'Food Risk Assessments Performed',
                'description' => 'Food risk assessments performed to identify, evaluate and mitigate food safety risks along the value chain.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public, Regulators',
                'department' => 'Food Safety Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'assessment_title', 'label' => 'Assessment Title', 'type' => 'string', 'required' => true],
                    ['name' => 'product_hazard', 'label' => 'Product/Hazard', 'type' => 'string', 'required' => true],
                    ['name' => 'risk_level', 'label' => 'Risk Level', 'type' => 'string', 'required' => true, 'options' => ['Low', 'Medium', 'High']],
                    ['name' => 'recommendations', 'label' => 'Recommendations', 'type' => 'text', 'required' => false],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Completed', 'Ongoing']],
                ],
            ],
            [
                'code' => 'TBS-MSC',
                'name' => 'Companies Certified under Management Systems',
                'description' => 'Companies certified under management systems (ISO 9001, ISO 22000, ISO 14001 and related schemes).',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Industry',
                'department' => 'Certification Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'company', 'label' => 'Company', 'type' => 'string', 'required' => true],
                    ['name' => 'management_system', 'label' => 'Management System', 'type' => 'string', 'required' => true, 'options' => ['ISO 9001', 'ISO 22000', 'ISO 14001', 'ISO 45001', 'Other']],
                    ['name' => 'certificate_no', 'label' => 'Certificate No.', 'type' => 'string', 'required' => true],
                    ['name' => 'validity_date', 'label' => 'Validity Date', 'type' => 'date', 'required' => false],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Valid', 'Expired', 'Suspended']],
                ],
            ],
            [
                'code' => 'TBS-FM',
                'name' => 'Fuel Marking Services Performed',
                'description' => 'Fuel marking services performed to control quality and curb adulteration and dumping of petroleum products.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, EWURA, TRA',
                'department' => 'Fuel Marking Unit',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'station_depot', 'label' => 'Station/Depot', 'type' => 'string', 'required' => true],
                    ['name' => 'volume_marked', 'label' => 'Volume Marked (Litres)', 'type' => 'number', 'required' => true],
                    ['name' => 'samples_taken', 'label' => 'Samples Taken', 'type' => 'integer', 'required' => true],
                    ['name' => 'samples_passed', 'label' => 'Samples Passed', 'type' => 'integer', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-PVOC',
                'name' => 'PVoC Inspection Certificates (CoCs) Issued',
                'description' => 'Certificates of Conformity (CoCs) issued under the Pre-Export Verification of Conformity (PVoC) Programme for imported products.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TRA, Importers',
                'department' => 'Inspection Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'coc_no', 'label' => 'CoC No.', 'type' => 'string', 'required' => true],
                    ['name' => 'importer', 'label' => 'Importer', 'type' => 'string', 'required' => true],
                    ['name' => 'product', 'label' => 'Product', 'type' => 'string', 'required' => true],
                    ['name' => 'country_of_origin', 'label' => 'Country of Origin', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Pending', 'Rejected']],
                ],
            ],
            [
                'code' => 'TBS-DI',
                'name' => 'Destination Inspection Batch Certificates Issued',
                'description' => 'Batch certificates issued under the Destination Inspection Programme for imported products.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TRA, Importers',
                'department' => 'Inspection Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'batch_certificate_no', 'label' => 'Batch Certificate No.', 'type' => 'string', 'required' => true],
                    ['name' => 'importer', 'label' => 'Importer', 'type' => 'string', 'required' => true],
                    ['name' => 'product', 'label' => 'Product', 'type' => 'string', 'required' => true],
                    ['name' => 'entry_point', 'label' => 'Entry Point', 'type' => 'string', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Issued', 'Pending', 'Rejected']],
                ],
            ],
            [
                'code' => 'TBS-UMV',
                'name' => 'Used Motor Vehicles Inspected and Certified',
                'description' => 'Inspection of imported used motor vehicles and issuance of inspection certificates.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TRA, Public',
                'department' => 'Inspection Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'certificate_no', 'label' => 'Certificate No.', 'type' => 'string', 'required' => true],
                    ['name' => 'importer', 'label' => 'Importer', 'type' => 'string', 'required' => true],
                    ['name' => 'vehicle', 'label' => 'Vehicle (Make/Model)', 'type' => 'string', 'required' => true],
                    ['name' => 'year_of_manufacture', 'label' => 'Year of Manufacture', 'type' => 'integer', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true, 'options' => ['Certified', 'Rejected', 'Pending']],
                ],
            ],
            [
                'code' => 'TBS-TAE',
                'name' => 'Technical Assistance to Exporters Provided',
                'description' => 'Technical assistance issued to exporters to meet standards and conformity requirements of destination markets.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Exporters, Industry',
                'department' => 'Exporter Support Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'exporter_company', 'label' => 'Exporter/Company', 'type' => 'string', 'required' => true],
                    ['name' => 'assistance_type', 'label' => 'Assistance Type', 'type' => 'string', 'required' => true],
                    ['name' => 'product_sector', 'label' => 'Product/Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'destination_market', 'label' => 'Destination Market', 'type' => 'string', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'TBS-WC',
                'name' => 'Wet Cargo Inspected',
                'description' => 'Inspection of wet cargo (in metric tonnes) such as edible oils, petroleum products and liquid bulk imports.',
                'institution' => 'TBS',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TRA, Importers',
                'department' => 'Inspection Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'vessel_cargo', 'label' => 'Vessel/Cargo', 'type' => 'string', 'required' => true],
                    ['name' => 'cargo_type', 'label' => 'Cargo Type', 'type' => 'string', 'required' => true],
                    ['name' => 'quantity_mt', 'label' => 'Quantity (MT)', 'type' => 'number', 'required' => true],
                    ['name' => 'inspection_result', 'label' => 'Inspection Result', 'type' => 'string', 'required' => true, 'options' => ['Conforming', 'Non-conforming', 'Pending']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
[
                'code' => 'BRELA-REG',
                'name' => 'Business Registrations and Licensing Indicators',
                'description' => 'Core registration and licensing indicators reported quarterly by BRELA: companies, business names, industrial licences, certificates, Class A licences, marks and patents.',
                'institution' => 'BRELA',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'fields' => [
                    [
                        'name' => 'new_companies_registered',
                        'label' => 'New Companies Registered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'companies_closed',
                        'label' => 'Companies Closed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'beneficial_ownership_records',
                        'label' => 'Beneficial Ownership Records Registered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'new_business_names',
                        'label' => 'New Business Names Registered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'industrial_licences_issued',
                        'label' => 'Industrial Licences Issued',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'registration_certificates_issued',
                        'label' => 'Certificates of Registration Issued',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'class_a_business_licences',
                        'label' => 'Class "A" Business Licences Issued',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'trademarks_registered',
                        'label' => 'Trade and Service Marks Registered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'patents_granted',
                        'label' => 'Patents Granted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'WMA-LM',
                'name' => 'Legal Metrology and Fair Trade Indicators',
                'description' => 'Weights and Measures Agency verification, calibration, inspection, enforcement and awareness indicators.',
                'institution' => 'WMA',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'fields' => [
                    [
                        'name' => 'instruments_verified',
                        'label' => 'Measuring Instruments Verified',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'regions_reverified',
                        'label' => 'Regions Covered by Annual Re-verification',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'secondary_standards_calibrated',
                        'label' => 'Secondary Standards Calibrated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'new_patterns_approved',
                        'label' => 'New Patterns of Instruments Approved',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'inspection_kits_procured',
                        'label' => 'Inspection Kits Procured',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'prepacked_goods_inspections',
                        'label' => 'Inspections on Pre-packed Goods',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'routine_surprise_inspections',
                        'label' => 'Routine and Surprise Inspections',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'cotton_scale_inspections',
                        'label' => 'Cotton Weighing Scale Inspections (Lake Zone)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'cashew_scale_inspections',
                        'label' => 'Cashew Weighing Scale Inspections (Southern Zone)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'border_compliance_verifications',
                        'label' => 'Compliance Verifications at Border Posts',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'licence_applicants_examined',
                        'label' => 'Licence Applicants Examined',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'cases_prosecuted',
                        'label' => 'Cases Prosecuted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'offences_compounded',
                        'label' => 'Offences Compounded in the Regions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'awareness_seminars',
                        'label' => 'Legal Awareness Seminars',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'public_exhibitions',
                        'label' => 'Public Exhibitions Participated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'NDC-PROJ',
                'name' => 'Strategic Industrial Projects Portfolio',
                'description' => 'Progress register for NDC strategic projects (Engaruka, Mchuchuma–Liganga, coal projects, KMTC, Mang\'ula, GTEA, Nyanza Glass, rubber estates).',
                'institution' => 'NDC',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TR',
                'fields' => [
                    [
                        'name' => 'project',
                        'label' => 'Project',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Engaruka Soda Ash',
                            'Mchuchuma-Liganga',
                            'Katewaka Coal',
                            'Mhukuru Coal',
                            'KMTC Industrial City',
                            'Mang\'ula Factory',
                            'GTEA Tyre Plant',
                            'Nyanza Glass',
                            'Rubber Estates',
                            'Other'
                        ]
                    ],
                    [
                        'name' => 'milestone',
                        'label' => 'Milestone / Activity',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'meetings_held',
                        'label' => 'Negotiation / Stakeholder Meetings',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'funds_mobilised_tzs',
                        'label' => 'Funds Mobilised (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'On track',
                            'Delayed',
                            'Completed',
                            'At risk'
                        ]
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'NDC-OPS',
                'name' => 'Industrial Operations and Investment',
                'description' => 'NDC production output, asset maintenance, investment and funds mobilisation indicators.',
                'institution' => 'NDC',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, TR',
                'fields' => [
                    [
                        'name' => 'machines_produced',
                        'label' => 'Machines Produced',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'spare_parts_produced',
                        'label' => 'Spare Parts Produced',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'rubber_produced_kg',
                        'label' => 'Dried Rubber Produced (kg)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'rubber_plantation_ha',
                        'label' => 'Rubber Plantation Expanded & Maintained (ha)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'industrial_parks_maintained',
                        'label' => 'Industrial Park Facilities Maintained',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'investment_properties_rehabilitated',
                        'label' => 'Investment Properties Rehabilitated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'projects_monitored',
                        'label' => 'Development Projects Monitored & Evaluated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'funds_mobilised_tzs',
                        'label' => 'Funds Mobilised (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'dividends_disbursed_tzs',
                        'label' => 'Dividend Payments Disbursed (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'CBE-EDU',
                'name' => 'Skills Development, Research and Innovation',
                'description' => 'College of Business Education enrolment, programmes, research, publications, consultancy, incubation and collaboration indicators.',
                'institution' => 'CBE',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, MoEST',
                'fields' => [
                    [
                        'name' => 'students_enrolled',
                        'label' => 'Students Enrolled',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'students_field_supervised',
                        'label' => 'Students Supervised in Field/Industrial Training',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'industrial_fields_solicited',
                        'label' => 'Industrial Fields Solicited for Placement',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'new_programmes',
                        'label' => 'New Academic Programmes Established',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'programmes_reviewed',
                        'label' => 'Academic Programmes Reviewed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'tracer_studies',
                        'label' => 'Tracer Studies Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'research_projects',
                        'label' => 'Research Projects Developed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'research_funds_tzs',
                        'label' => 'Research Funds Solicited (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'publications_local',
                        'label' => 'Publications in Local Journals',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'publications_international',
                        'label' => 'Publications in International Journals',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'consultancies',
                        'label' => 'Consultancies Carried Out',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'short_courses',
                        'label' => 'Short Courses and Review Classes Offered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'incubation_programmes',
                        'label' => 'Incubation Programmes Coordinated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'entrepreneurship_clubs',
                        'label' => 'Entrepreneurship Clubs Established',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'youth_practitioners_trained',
                        'label' => 'Youth and Informal Practitioners Trained',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'mous_signed',
                        'label' => 'MoUs Signed (Local & International)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'funds_mobilised_tzs',
                        'label' => 'Funds Mobilised (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'WRRB-WRS',
                'name' => 'Warehouse Receipt System Operations',
                'description' => 'Warehouse licensing inspections, audits, commodities traded under the Warehouse Receipt System and revenue collection.',
                'institution' => 'WHLB',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, TRA',
                'fields' => [
                    [
                        'name' => 'warehouses_inspected',
                        'label' => 'Licensed Warehouses Inspected',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'compliance_inspections',
                        'label' => 'Routine Quality Compliance Inspections',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'performance_audits',
                        'label' => 'Performance Audits of Licence Holders',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'commodities_traded_wrs',
                        'label' => 'Commodities Traded under WRS',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'stakeholder_workshops',
                        'label' => 'Stakeholder Workshops and Seminars',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'revenue_collected_tzs',
                        'label' => 'Revenue Collected (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'receivables_collected_tzs',
                        'label' => 'Receivables Collected (TZS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'TANTRADE-PRO',
                'name' => 'Trade Promotion and Market Linkages',
                'description' => 'TanTrade value-chain assessments, market information, SME training (by gender), business clinics, trade missions and B2B linkages.',
                'institution' => 'TANTRADE',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'fields' => [
                    [
                        'name' => 'value_chain_assessments',
                        'label' => 'Value Chain Assessments Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'research_activities',
                        'label' => 'Research Activities Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'market_info_datasets',
                        'label' => 'Market Information Datasets Published',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'company_profiles_published',
                        'label' => 'Company Profiles Published (Directory & e-Trade)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'smes_trained_male',
                        'label' => 'SMEs Trained — Male-owned',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'smes_trained_female',
                        'label' => 'SMEs Trained — Female-owned',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'enterprises_coached',
                        'label' => 'Enterprises Receiving Coaching & Mentoring',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'business_clinics',
                        'label' => 'Business Clinic Services Delivered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'trade_mission_participants',
                        'label' => 'Enterprises in Overseas Trade Missions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'b2b_meetings',
                        'label' => 'Enterprises in B2B / B2C / B2G Meetings',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'border_markets_strengthened',
                        'label' => 'Border Markets Strengthened',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'crossborder_programmes',
                        'label' => 'Cross-border Trade Programmes Coordinated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'FCC-CPE',
                'name' => 'Competition Enforcement and Consumer Protection',
                'description' => 'FCC enforcement actions, complaints, mergers, exemptions, standard-form consumer contracts, surveillance and counterfeit control indicators.',
                'institution' => 'FCC',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, FCT, Public',
                'fields' => [
                    [
                        'name' => 'enforcement_actions',
                        'label' => 'Enforcement Actions (Findings, Orders, Settlements)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'final_findings',
                        'label' => 'Final Findings Issued',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'complaints_investigated',
                        'label' => 'Complaints Investigated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'complaints_resolved',
                        'label' => 'Complaints Resolved',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'consumer_complaints_redressed',
                        'label' => 'Consumer Complaints Redressed or Advised',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'appeals_coordinated',
                        'label' => 'Appeals Coordinated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'mergers_investigated',
                        'label' => 'Mergers Investigated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'exemptions_investigated',
                        'label' => 'Exemptions Investigated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'research_studies',
                        'label' => 'Research Studies Completed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'advocacy_sessions',
                        'label' => 'Advocacy Sessions Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'sfcc_reviewed',
                        'label' => 'Standard Form Consumer Contracts Reviewed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'sfcc_approved',
                        'label' => 'SFCC Certificates Approved',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'price_surveillance',
                        'label' => 'Price Surveillance Activities',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'market_surveillance',
                        'label' => 'Market Surveillance Activities',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'raids',
                        'label' => 'Raids Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'inspections',
                        'label' => 'Inspections Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'seizures',
                        'label' => 'Seizures at Ports and ICDs',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'counterfeit_disposals',
                        'label' => 'Counterfeit Goods Disposal Exercises',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'TEMDO-ENG',
                'name' => 'Engineering Design and Technology Development',
                'description' => 'TEMDO prototypes, machines and technologies developed, fabricated and deployed, and commercialisation indicators.',
                'institution' => 'TEMDO',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'fields' => [
                    [
                        'name' => 'prototypes_developed',
                        'label' => 'Prototypes Developed and Fabricated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'machines_deployed',
                        'label' => 'Machines and Technologies Deployed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'incinerators_installed',
                        'label' => 'Incinerators Designed, Fabricated and Installed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'medical_equipment_fabricated',
                        'label' => 'Hospital & Medical Equipment Fabricated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'pilot_technologies',
                        'label' => 'Pilot Technologies Manufactured',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'contract_manufacturing_jobs',
                        'label' => 'Contract Design & Manufacture Jobs',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'markets_reached',
                        'label' => 'Markets Reached for Commercialisation',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'proposals_prepared',
                        'label' => 'Commercialisation Project Proposals',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'CAMARTEC-AGT',
                'name' => 'Agricultural and Rural Technologies',
                'description' => 'CAMARTEC agricultural technologies fabricated and tested, mobile processing factories, farmer training and extension services.',
                'institution' => 'CARMATEC',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'fields' => [
                    [
                        'name' => 'technologies_fabricated',
                        'label' => 'Agricultural Technologies Fabricated & Tested',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'prototypes_tested',
                        'label' => 'Prototype Planter Models Tested',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'mobile_factories_established',
                        'label' => 'Mobile Processing Factories Established',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'farmers_trained',
                        'label' => 'Processors and Smallholder Farmers Trained',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'machines_inspected_tested',
                        'label' => 'Machines and Technologies Inspected & Tested',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'consultancy_sessions',
                        'label' => 'Consultancy, Training & Extension Sessions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'TIRDO-RDI',
                'name' => 'Industrial Research and Innovation',
                'description' => 'TIRDO industrial CERT assessments, R&D partnerships, training, industrial visits and technology-transfer collaboration.',
                'institution' => 'TIRDO',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'fields' => [
                    [
                        'name' => 'vulnerability_assessments',
                        'label' => 'Vulnerability Assessments (Industrial CERT)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'partnerships_established',
                        'label' => 'Partnerships with Sector Associations',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'trainings_conducted',
                        'label' => 'Training Sessions (Instrument Repair & Maintenance)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'industrial_visits',
                        'label' => 'Industrial Visits for Technology Transfer',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'proposal_workshops',
                        'label' => 'Project Proposal-writing Workshops Attended',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'SIDO-CRD',
                'name' => 'SME Credit, Skills and Entrepreneurship',
                'description' => 'SIDO credit facilitation (NEDF/CGS), skills development, entrepreneurship campaigns, jobs created and loan portfolio quality.',
                'institution' => 'SIDO',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'fields' => [
                    [
                        'name' => 'new_small_medium_industries',
                        'label' => 'New Small/Medium Industries Established',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'startups_loaned',
                        'label' => 'Start-up Companies Financed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'innovations_loaned',
                        'label' => 'Innovations/Inventions Financed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'small_businesses_loaned',
                        'label' => 'Small Businesses Financed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'youth_loaned',
                        'label' => 'Youth Financed (MIT Sectors)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'loans_value_tzs_bn',
                        'label' => 'Value of Loans (TZS bn)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'youth_skills_trained',
                        'label' => 'Youth Trained in Skills',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'new_direct_jobs',
                        'label' => 'New Direct Jobs Created',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'npl_percent',
                        'label' => 'Non-performing Loans (%)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'entrepreneurship_campaigns',
                        'label' => 'Entrepreneurship Campaigns (Regions)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
        ];

        foreach ($datasets as $data) {
            $institution = Institution::where('code', $data['institution'])->firstOrFail();
            unset($data['institution']);
            Dataset::firstOrCreate(['code' => $data['code']], $data + ['institution_id' => $institution->id]);
        }

        // Ministry-internal datasets owned by ministry departments (no institution).
        // Department data officers submit these exactly like institutional officers.
        $ministryDatasets = [
            [
                'code' => 'MIT-ILR',
                'name' => 'Industrial Licensing Register',
                'description' => 'Industrial licences issued, renewed, suspended or cancelled by the Industry Department.',
                'department_code' => 'IND',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TR, Public',
                'department' => 'Industry Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date Issued', 'type' => 'date', 'required' => true],
                    ['name' => 'business_name', 'label' => 'Business Name', 'type' => 'string', 'required' => true],
                    ['name' => 'licence_type', 'label' => 'Licence Type', 'type' => 'string', 'required' => true,
                        'options' => ['Manufacturing', 'Agro-processing', 'Mining support', 'Industrial services']],
                    ['name' => 'region', 'label' => 'Region', 'type' => 'string', 'required' => true],
                    ['name' => 'capital_investment', 'label' => 'Capital Investment (TZS m)', 'type' => 'number', 'required' => true],
                    ['name' => 'employment', 'label' => 'Employment', 'type' => 'integer', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'string', 'required' => true,
                        'options' => ['New', 'Renewed', 'Suspended', 'Cancelled']],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'MIT-IPS',
                'name' => 'Industrial Production Statistics',
                'description' => 'Production volumes and capacity utilisation by industrial subsector, compiled by the Industry Department.',
                'department_code' => 'IND',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, NBS, TR',
                'department' => 'Industry Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'subsector', 'label' => 'Subsector', 'type' => 'string', 'required' => true,
                        'options' => ['Agro-processing', 'Textiles and garments', 'Cement and construction materials', 'Plastics and packaging', 'Beverages', 'Other']],
                    ['name' => 'production_volume', 'label' => 'Production Volume', 'type' => 'number', 'required' => true],
                    ['name' => 'unit', 'label' => 'Unit of Measure', 'type' => 'string', 'required' => true],
                    ['name' => 'capacity_utilization', 'label' => 'Capacity Utilisation (%)', 'type' => 'number', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'MIT-TFS',
                'name' => 'Trade Facilitation Statistics',
                'description' => 'Cross-border trade values and clearance times at major border posts, compiled by the Trade Department.',
                'department_code' => 'TRD',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, TRA, WTO',
                'department' => 'Trade Department',
                'fields' => [
                    ['name' => 'period', 'label' => 'Period', 'type' => 'string', 'required' => true],
                    ['name' => 'border_post', 'label' => 'Border Post', 'type' => 'string', 'required' => true],
                    ['name' => 'exports_value_musd', 'label' => 'Exports Value (USD m)', 'type' => 'number', 'required' => true],
                    ['name' => 'imports_value_musd', 'label' => 'Imports Value (USD m)', 'type' => 'number', 'required' => true],
                    ['name' => 'clearance_time_days', 'label' => 'Avg Clearance Time (days)', 'type' => 'number', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'MIT-EPA',
                'name' => 'Export Promotion Activities',
                'description' => 'Trade fairs, buyer missions and export-readiness support delivered by the Trade Department.',
                'department_code' => 'TRD',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Exporters',
                'department' => 'Trade Department',
                'fields' => [
                    ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'activity', 'label' => 'Activity', 'type' => 'string', 'required' => true],
                    ['name' => 'sector', 'label' => 'Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'participating_firms', 'label' => 'Participating Firms', 'type' => 'integer', 'required' => true],
                    ['name' => 'export_deals_value_musd', 'label' => 'Export Deals (USD m)', 'type' => 'number', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'code' => 'MIT-SPR',
                'name' => 'Sector Performance Report',
                'description' => 'Annual sector performance indicators — GDP contribution, employment and growth — compiled by Policy and Planning.',
                'department_code' => 'PPP',
                'frequency' => 'annually',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, NBS, Public',
                'department' => 'Policy and Planning Department',
                'fields' => [
                    ['name' => 'year', 'label' => 'Year', 'type' => 'integer', 'required' => true],
                    ['name' => 'sector', 'label' => 'Sector', 'type' => 'string', 'required' => true],
                    ['name' => 'gdp_contribution_pct', 'label' => 'GDP Contribution (%)', 'type' => 'number', 'required' => true],
                    ['name' => 'employment', 'label' => 'Employment', 'type' => 'integer', 'required' => true],
                    ['name' => 'growth_rate_pct', 'label' => 'Growth Rate (%)', 'type' => 'number', 'required' => true],
                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
                ],
            ],
[
                'code' => 'MIT-HR-SDW',
                'name' => 'Staff Development, Welfare and Compliance Programmes',
                'description' => 'DAHRM: staff training (short/long courses), statutory meetings, professional workshops, HIV/AIDS & NCD interventions, anti-corruption programmes and PE estimates.',
                'department_code' => 'DAHRM',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Dept of Administration and HRM',
                'fields' => [
                    [
                        'name' => 'staff_trained_short',
                        'label' => 'Staff Trained — Short Courses',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'staff_trained_long',
                        'label' => 'Staff Trained — Long Courses',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'statutory_meetings_supported',
                        'label' => 'Statutory Meetings & National Ceremonies Facilitated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'professional_workshops_supported',
                        'label' => 'Professional Meetings & Workshops Facilitated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'hiv_ncd_interventions',
                        'label' => 'HIV/AIDS & NCD Intervention Programmes',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'anticorruption_programmes',
                        'label' => 'Anti-corruption Programmes Implemented',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'staff_welfare_interventions',
                        'label' => 'Staff Welfare & Hospitality Interventions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-IND-CAP',
                'name' => 'Industrial Capacity, Production and Employment',
                'description' => 'DID: production capacity, actual production, real demand and employment (direct & indirect) by industry type, including closed industries.',
                'department_code' => 'IND',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, NBS, TR',
                'department' => 'Department of Industrial Development',
                'fields' => [
                    [
                        'name' => 'industry_type',
                        'label' => 'Industry Type (Aina ya kiwanda)',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Bati (roofing sheets)',
                            'Cement (saruji)',
                            'Sugar (sukari)',
                            'Cashew processing (korosho)',
                            'Fertilizer — large',
                            'Fertilizer — medium',
                            'Pharmaceuticals & medical supplies',
                            'Edible oils',
                            'Large industries',
                            'Medium industries',
                            'Small industries',
                            'New industries',
                            'Closed industries'
                        ]
                    ],
                    [
                        'name' => 'production_capacity',
                        'label' => 'Production Capacity (Uwezo wa uzalishaji)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'actual_production',
                        'label' => 'Actual Production (Uzalishaji halisi)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'actual_demand',
                        'label' => 'Real Demand (Mahitaji halisi)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'direct_jobs',
                        'label' => 'Direct Jobs (Ajira za moja kwa moja)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'indirect_jobs',
                        'label' => 'Indirect Jobs (Ajira zisizo za moja kwa moja)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Operating',
                            'Closed'
                        ]
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-IND-DEV',
                'name' => 'Industrial Development Initiatives',
                'description' => 'DID: strategies and legislation, diagnostic studies, industrial surveys, investor research, KAIZEN, consultative forums and coordination of TEMDO/NDC/CAMARTEC/TIRDO projects.',
                'department_code' => 'IND',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Department of Industrial Development',
                'fields' => [
                    [
                        'name' => 'strategies_reviewed',
                        'label' => 'Strategies / Plans / Legislation Developed or Reviewed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'diagnostic_studies',
                        'label' => 'Industrial Diagnostic Studies',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'industrial_surveys',
                        'label' => 'Industrial Surveys Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'investor_research_studies',
                        'label' => 'Investor Research Studies',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'kaizen_interventions',
                        'label' => 'KAIZEN Quality & Productivity Interventions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'consultative_forums',
                        'label' => 'Sub-sectoral Consultative Forums',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'rd_collaborations',
                        'label' => 'R&D / Technology-transfer Collaborations',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'projects_coordinated',
                        'label' => 'Institutional Projects Coordinated (TEMDO, NDC, CAMARTEC, TIRDO)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-PPP-PERF',
                'name' => 'MTEF Activity Performance and Budget Execution',
                'description' => 'DPP result-framework reporting per the warehouse dimension table: objective, target, activity, output indicator, baseline, annual target, actual, planned budget, disbursed and expenditure.',
                'department_code' => 'PPP',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TR, PO-PSM',
                'department' => 'Department of Policy and Planning',
                'fields' => [
                    [
                        'name' => 'objective_code',
                        'label' => 'Objective Code',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'AF',
                            'Y',
                            'X',
                            'A',
                            'B',
                            'C',
                            'D',
                            'E'
                        ]
                    ],
                    [
                        'name' => 'target',
                        'label' => 'Result-framework Target',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'activity',
                        'label' => 'Activity',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'output_indicator',
                        'label' => 'Output Indicator',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'baseline',
                        'label' => 'Baseline',
                        'type' => 'number',
                        'required' => false
                    ],
                    [
                        'name' => 'annual_target',
                        'label' => 'Annual Target',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'actual_value',
                        'label' => 'Actual (this period)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'planned_budget_tzs',
                        'label' => 'Planned Budget (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'disbursed_tzs',
                        'label' => 'Disbursed (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'expenditure_tzs',
                        'label' => 'Expenditure (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'key_activity',
                        'label' => 'Key Activity',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Yes',
                            'No'
                        ]
                    ]
                ]
            ],
[
                'code' => 'MIT-DTD-PROM',
                'name' => 'Trade Development and Market Intelligence',
                'description' => 'DTD: MPPD dialogues, business conferences, licence inspections, trade formalization, exhibitions (DITF, Nane Nane), digital marketing and market information dissemination.',
                'department_code' => 'DTD',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Department of Trade and Development',
                'fields' => [
                    [
                        'name' => 'mppd_meetings',
                        'label' => 'Ministerial Public-Private Dialogues (MPPD)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'business_conferences',
                        'label' => 'Conferences / Meetings on Business Challenges',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'licence_inspections',
                        'label' => 'Business Licence Inspections',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'formalized_enterprises',
                        'label' => 'Enterprises Formalized',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'exhibitions_coordinated',
                        'label' => 'Exhibitions Coordinated (DITF, Nane Nane, Expos)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'digital_marketing_campaigns',
                        'label' => 'Digital Marketing Campaigns',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'market_intelligence_reports',
                        'label' => 'Market Intelligence Reports',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'commodity_price_datasets',
                        'label' => 'Commodity Market Information Datasets Disseminated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-DTI-TRADE',
                'name' => 'Merchandise Trade by Market',
                'description' => 'DTI: exports and imports (USD) between Tanzania and major markets — World, EU, EAC, SADC, Asia, China, India, Japan (Bidhaa zilizouzwa nje / zilizoingizwa nchini).',
                'department_code' => 'DTI',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'source_system' => null,
                'consumers' => 'MIT, TRA, BoT',
                'department' => 'Department of Trade Integration',
                'fields' => [
                    [
                        'name' => 'market',
                        'label' => 'Market (Eneo)',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'World',
                            'European Union',
                            'EAC',
                            'SADC',
                            'Asia',
                            'China',
                            'India',
                            'Japan'
                        ]
                    ],
                    [
                        'name' => 'exports_usd',
                        'label' => 'Exports (USD m) — Bidhaa zilizouzwa nje',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'imports_usd',
                        'label' => 'Imports (USD m) — Bidhaa zilizoingizwa nchini',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-DTI-ACCESS',
                'name' => 'Trade Integration and Market Access',
                'description' => 'DTI: negotiations (EAC, AfCFTA, TFTA, SADC, AGOA), TBT/SPS/TFA committees, NTB elimination, e-commerce, trade remedies, missions and trade agreements.',
                'department_code' => 'DTI',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Department of Trade Integration',
                'fields' => [
                    [
                        'name' => 'negotiations_participated',
                        'label' => 'Market-access Negotiations Participated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'committee_meetings',
                        'label' => 'TBT/SPS/TFA & National Committee Meetings',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'awareness_workshops',
                        'label' => 'Stakeholder Awareness Workshops',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'ntbs_addressed',
                        'label' => 'NTBs Addressed / Eliminated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'trade_missions',
                        'label' => 'Trade Missions & Business Forums',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'jtc_jpc_meetings',
                        'label' => 'JTC / JPC Meetings (Strategic Countries)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'agreements_concluded',
                        'label' => 'MoUs / Trade Agreements Concluded',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'ecommerce_initiatives',
                        'label' => 'E-Commerce Strategy Initiatives',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-SME-DEV',
                'name' => 'SME Development and Formalisation',
                'description' => 'SME Department: SME policy review, loan-scheme awareness, formalization, industrial clusters, LGA shaded premises and MSME market access.',
                'department_code' => 'SME',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, SIDO',
                'department' => 'Department of Small and Medium Enterprises',
                'fields' => [
                    [
                        'name' => 'loan_scheme_awareness_campaigns',
                        'label' => 'Loan-scheme Awareness Campaigns',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'enterprises_formalized',
                        'label' => 'Informal Enterprises Formalized',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'clusters_supported',
                        'label' => 'SME Industrial Clusters Supported',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'lga_premises_supported',
                        'label' => 'LGAs Supported to Construct SME Premises',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'smes_linked_to_markets',
                        'label' => 'SMEs Linked to Regional/International Markets',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'sensitization_meetings',
                        'label' => 'Sensitization Meetings (incl. food processing technology)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-FAU-FIN',
                'name' => 'Financial Management and Reporting',
                'description' => 'FAU: payroll processing for MIT HQ and institutions, audit queries, financial statements, financial reports, voucher lists and disbursements.',
                'department_code' => 'FAU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, TR',
                'department' => 'Finance and Accounting Unit',
                'fields' => [
                    [
                        'name' => 'payrolls_processed',
                        'label' => 'Payrolls Processed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'institutions_paid',
                        'label' => 'Institutions Paid',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'audit_queries_answered',
                        'label' => 'Audit Queries Answered',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'audit_queries_pending',
                        'label' => 'Audit Queries Pending',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'financial_statements_prepared',
                        'label' => 'Financial Statements Prepared',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'financial_reports_submitted',
                        'label' => 'Monthly/Quarterly Financial Reports Submitted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'voucher_lists_submitted',
                        'label' => 'Voucher Lists Submitted to Treasury',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'disbursements_tzs',
                        'label' => 'Disbursements to Institutions (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-ICT-SYS',
                'name' => 'ICT Systems and Capacity',
                'description' => 'ICTU: network/system availability, industrial databases and the MIT data warehouse, ICT governance and staff capacity building.',
                'department_code' => 'ICTU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'ICT Unit',
                'fields' => [
                    [
                        'name' => 'network_uptime_percent',
                        'label' => 'LAN/Internet/Email Uptime (%)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'systems_maintained',
                        'label' => 'Computer Systems Maintained',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'databases_maintained',
                        'label' => 'Industrial Databases / Data Warehouse Maintained',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'ict_governance_initiatives',
                        'label' => 'ICT Governance Initiatives',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'ict_trainings',
                        'label' => 'ICT Awareness Trainings Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'staff_trained_ict',
                        'label' => 'Staff Trained in ICT',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-PMU-PROC',
                'name' => 'Procurement and Asset Management',
                'description' => 'PMU: annual procurement plan execution, tenders, stocktaking, asset verification/disposal, inventory and Tender Board services.',
                'department_code' => 'PMU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, PPRA',
                'department' => 'Procurement Management Unit',
                'fields' => [
                    [
                        'name' => 'procurement_plan_items',
                        'label' => 'Procurement Plan Items Executed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'tenders_completed',
                        'label' => 'Tenders Completed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'stocktaking_exercises',
                        'label' => 'Stocktaking Exercises Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'assets_verified',
                        'label' => 'Assets Physically Verified & Codified',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'assets_disposed',
                        'label' => 'Assets Disposed',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'tender_board_meetings',
                        'label' => 'Tender Board Meetings Facilitated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-LSU-LEGAL',
                'name' => 'Legal Matters Register',
                'description' => 'LSU: legal matters handled — advice, opinions, contracts, MoUs, guarantees, consultancies and disputes — with compliance and status.',
                'department_code' => 'LSU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Legal Services Unit',
                'fields' => [
                    [
                        'name' => 'date_received',
                        'label' => 'Date Received',
                        'type' => 'date',
                        'required' => true
                    ],
                    [
                        'name' => 'matter_type',
                        'label' => 'Type of Matter',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Advice',
                            'Opinion',
                            'Contract',
                            'MoU',
                            'Guarantee',
                            'Consultancy',
                            'Dispute',
                            'Other'
                        ]
                    ],
                    [
                        'name' => 'requesting_unit',
                        'label' => 'Requesting Directorate / Unit',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Brief Description',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'action_taken',
                        'label' => 'Action Taken',
                        'type' => 'string',
                        'required' => false
                    ],
                    [
                        'name' => 'compliant',
                        'label' => 'Compliant with Law & Directives',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Yes',
                            'No',
                            'Partly'
                        ]
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Open',
                            'Closed'
                        ]
                    ]
                ]
            ],
[
                'code' => 'MIT-LSU-CONT',
                'name' => 'Contracts, MoUs and Guarantees Follow-up',
                'description' => 'LSU: register of contracts, MoUs and guarantees under follow-up — parties, duration, value, registration and compliance status.',
                'department_code' => 'LSU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Legal Services Unit',
                'fields' => [
                    [
                        'name' => 'title',
                        'label' => 'Title of Contract / MoU / Guarantee',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'parties',
                        'label' => 'Parties',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'name' => 'start_date',
                        'label' => 'Start Date',
                        'type' => 'date',
                        'required' => true
                    ],
                    [
                        'name' => 'expiry_date',
                        'label' => 'Expiry Date',
                        'type' => 'date',
                        'required' => true
                    ],
                    [
                        'name' => 'value_tzs',
                        'label' => 'Value (TZS)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'registered',
                        'label' => 'Registered in the Register',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Yes',
                            'No'
                        ]
                    ],
                    [
                        'name' => 'compliance_status',
                        'label' => 'Compliance & Timeliness Status',
                        'type' => 'string',
                        'required' => true,
                        'options' => [
                            'Compliant',
                            'Attention needed',
                            'Overdue'
                        ]
                    ]
                ]
            ],
[
                'code' => 'MIT-ME-PERF',
                'name' => 'Monitoring, Evaluation and Institutional Performance',
                'description' => 'M&E Unit: NKRA reporting, evaluations and impact assessments, institutional performance reviews (PIPMIS) and follow-up of evaluation recommendations.',
                'department_code' => 'ME',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, PO-PSM',
                'department' => 'Monitoring and Evaluation Unit',
                'fields' => [
                    [
                        'name' => 'nkra_reports',
                        'label' => 'NKRA Performance Reports Prepared',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'evaluations_conducted',
                        'label' => 'Evaluations Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'impact_assessments',
                        'label' => 'Impact Assessments Undertaken',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'institutions_reviewed',
                        'label' => 'Institutions Reviewed (PIPMIS)',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'manifesto_directives_tracked',
                        'label' => 'Manifesto / Government Directives Tracked',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'recommendations_followed_up',
                        'label' => 'Evaluation Recommendations Followed Up',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-IAU-AUDIT',
                'name' => 'Internal Audit Engagements',
                'description' => 'IAU: risk-based audit plan execution, engagements, findings issued and resolved, performance audits of institutions and audit committee facilitation.',
                'department_code' => 'IAU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT',
                'department' => 'Internal Audit Unit',
                'fields' => [
                    [
                        'name' => 'audits_planned',
                        'label' => 'Audit Engagements Planned',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'audits_conducted',
                        'label' => 'Audit Engagements Conducted',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'findings_issued',
                        'label' => 'Audit Findings Issued',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'findings_resolved',
                        'label' => 'Audit Findings Resolved',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'performance_audits',
                        'label' => 'Performance Audits of Institutions',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'project_audits',
                        'label' => 'Project Audits',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'audit_committee_meetings',
                        'label' => 'Audit Committee Meetings Facilitated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'quarterly_reports',
                        'label' => 'Quarterly Consolidated Audit Reports',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
[
                'code' => 'MIT-GCU-COMM',
                'name' => 'Government Communication and Public Engagement',
                'description' => 'GCU: press briefings, media engagement, ministerial articles and publications, campaigns and management of official online channels.',
                'department_code' => 'GCU',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'source_system' => null,
                'consumers' => 'MIT, Public',
                'department' => 'Government Communication Unit',
                'fields' => [
                    [
                        'name' => 'press_briefings',
                        'label' => 'Press Briefings Coordinated',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'media_engagements',
                        'label' => 'Media Dialogues / Engagements',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'articles_published',
                        'label' => 'Ministerial Articles & Newspaper Features',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'awareness_campaigns',
                        'label' => 'Sensitization & Advocacy Campaigns',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'website_updates',
                        'label' => 'Website / Social Media Updates',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ],
        ];

        foreach ($ministryDatasets as $data) {
            $department = Department::where('code', $data['department_code'])->firstOrFail();
            unset($data['department_code']);
            Dataset::firstOrCreate(['code' => $data['code']], $data + [
                'institution_id' => null,
                'department_id' => $department->id,
            ]);
        }
    }
}
