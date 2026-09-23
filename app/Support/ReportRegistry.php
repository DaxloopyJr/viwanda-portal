<?php

namespace App\Support;

/**
 * Registry of the management, operational and public reports proposed in
 * "Proposed Management and Public Reports for National Growth Decision-Making"
 * (derived from the FCT and TBS data parameters for the data warehouse).
 *
 * Each definition declares its tier, audience, source data parameters and the
 * indicator sections the ThematicReportEngine computes from approved
 * submission records.
 *
 * Indicator types:
 *   count        number of records in the given dataset(s)
 *   sum          sum of a numeric field
 *   avg          average of a numeric field
 *   ratio        sum(numerator) / sum(denominator) * 100
 *   count_where  records whose field matches value(s) (exact or contains)
 *   rate_where   count_where / total records * 100
 *   distinct     number of distinct non-empty values of a field
 *   due_within   records whose date field falls within the next N days
 *
 * Formats: number | percent | money | days | months | mt | usd
 */
class ReportRegistry
{
    public const TIERS = [
        1 => ['label' => 'Tier 1 — Executive Reports for Ministry Management', 'icon' => 'bi-briefcase', 'color' => 'navy',
            'note' => 'High-level quarterly decision reports for the Permanent Secretary and sector leadership. Computed from Ministry-approved data (accepted and published).'],
        2 => ['label' => 'Tier 2 — Operational Reports for Institutional Management', 'icon' => 'bi-gear', 'color' => 'teal',
            'note' => 'Internal management reports that help FCT and TBS leadership run their institutions efficiently. Computed from Ministry-approved data (accepted and published).'],
        3 => ['label' => 'Tier 3 — Public-Facing Reports', 'icon' => 'bi-megaphone', 'color' => 'green',
            'note' => 'Simplified, publishable outputs that communicate government performance. Computed strictly from PUBLISHED (Ministry-validated) data, per the implementation notes.'],
        4 => ['label' => 'Tier 4 — National Growth Reports (Departments & Subordinate Institutions)', 'icon' => 'bi-graph-up-arrow', 'color' => 'gold',
            'note' => 'Standard and dynamic reports generated from the datasets that Ministry departments, units and subordinate institutions submit regularly (per the MIT & Institutions data-requirements document). Computed from Ministry-approved data (accepted and published).'],
    ];

    public static function all(): array
    {
        return [
            'r1' => [
                'key' => 'R1', 'tier' => 1,
                'title' => 'Institutional Performance Scorecard',
                'audience' => 'Ministry management', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'Consolidated quarterly scorecard per institution: cases resolved vs due and resolution rate (FCT); average disposal time; complaints resolved within timeframe; client response days; TBS service volumes (certifications, permits, tests, calibrations) against targets.',
                'decisions' => 'Resource allocation; performance contract reviews; early warning on institutions falling behind service standards.',
                'parameters' => 'FCT: Cases Adjudicated; Resolved vs Due for Hearing; Average Disposal Time; Complaints Resolved within Timeframe; Average Client Response Time. TBS: all service-volume parameters.',
                'datasets' => ['FCT-CA', 'FCT-RC', 'FCT-ATD', 'FCT-CR', 'FCT-ART', 'TBS-PCI', 'TBS-MSC', 'TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-PST', 'TBS-EC'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Cases adjudicated', 'type' => 'count', 'datasets' => ['FCT-CA'], 'basis' => 'FCT-CA records'],
                        ['label' => 'Cases resolved', 'type' => 'sum', 'datasets' => ['FCT-RC'], 'field' => 'cases_resolved', 'basis' => 'Σ FCT-RC.cases_resolved'],
                        ['label' => 'Cases due for hearing', 'type' => 'sum', 'datasets' => ['FCT-RC'], 'field' => 'cases_due', 'basis' => 'Σ FCT-RC.cases_due'],
                        ['label' => 'Resolution rate', 'type' => 'ratio', 'datasets' => ['FCT-RC'], 'numerator' => 'cases_resolved', 'denominator' => 'cases_due', 'format' => 'percent', 'basis' => 'resolved ÷ due'],
                        ['label' => 'Average disposal time', 'type' => 'avg', 'datasets' => ['FCT-ATD'], 'field' => 'average_months', 'format' => 'months', 'basis' => 'avg FCT-ATD.average_months'],
                        ['label' => 'Complaints resolved within timeframe', 'type' => 'ratio', 'datasets' => ['FCT-CR'], 'numerator' => 'resolved_within_timeframe', 'denominator' => 'complaints_received', 'format' => 'percent', 'basis' => 'within timeframe ÷ received'],
                        ['label' => 'Average client response', 'type' => 'avg', 'datasets' => ['FCT-ART'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg FCT-ART.average_response_days'],
                        ['label' => 'Certifications issued (TBS)', 'type' => 'count', 'datasets' => ['TBS-PCI', 'TBS-MSC'], 'basis' => 'TBS-PCI + TBS-MSC records'],
                        ['label' => 'Permits issued (TBS)', 'type' => 'count', 'datasets' => ['TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI'], 'basis' => 'FCR + PRP + PVoC + DI records'],
                        ['label' => 'Product tests performed', 'type' => 'count', 'datasets' => ['TBS-PST'], 'basis' => 'TBS-PST records'],
                        ['label' => 'Equipment calibrations', 'type' => 'count', 'datasets' => ['TBS-EC'], 'basis' => 'TBS-EC records'],
                    ]],
                    ['type' => 'trend', 'title' => 'Service volumes by reporting period', 'datasets' => ['FCT-CA', 'TBS-PCI', 'TBS-MSC', 'TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-PST', 'TBS-EC'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r2' => [
                'key' => 'R2', 'tier' => 1,
                'title' => 'Market Fairness and Competition Health Report',
                'audience' => 'Ministry management', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'Competition and regulatory case trends by authority (FCC, EWURA, TCRA, TCAA, LATRA, PURA) and subject matter (mergers & acquisitions, energy, petroleum, telecoms); outcomes distribution; backlog ageing.',
                'decisions' => 'Identifies sectors with rising disputes or concentration concerns; informs MIT policy interventions and regulatory reviews that protect consumers and enable investment.',
                'parameters' => 'FCT: Appeal/Application Case; Cases Adjudicated; Economic Studies on Competition and Regulated Sectors.',
                'datasets' => ['FCT-AC', 'FCT-CA', 'FCT-ES'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Appeals/applications filed', 'type' => 'count', 'datasets' => ['FCT-AC'], 'basis' => 'FCT-AC records'],
                        ['label' => 'Cases adjudicated', 'type' => 'count', 'datasets' => ['FCT-CA'], 'basis' => 'FCT-CA records'],
                        ['label' => 'Economic studies completed', 'type' => 'count', 'datasets' => ['FCT-ES'], 'basis' => 'FCT-ES records'],
                        ['label' => 'Open backlog (pending/withdrawn-excluded)', 'type' => 'count_where', 'datasets' => ['FCT-AC'], 'field' => 'case_status', 'values' => ['Pending hearing', 'Under review', 'Part-heard'], 'match' => 'contains', 'basis' => 'FCT-AC pending statuses'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Cases by originating authority', 'dataset' => 'FCT-AC', 'field' => 'case_originated'],
                    ['type' => 'breakdown', 'title' => 'Cases by subject matter / sector', 'dataset' => 'FCT-AC', 'field' => 'case_sector'],
                    ['type' => 'breakdown', 'title' => 'Outcomes distribution', 'dataset' => 'FCT-AC', 'field' => 'case_status'],
                    ['type' => 'breakdown', 'title' => 'Determinations by authority', 'dataset' => 'FCT-CA', 'field' => 'authority'],
                    ['type' => 'trend', 'title' => 'Case filings by reporting period', 'datasets' => ['FCT-AC', 'FCT-CA'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r3' => [
                'key' => 'R3', 'tier' => 1,
                'title' => 'Standards, Quality and Competitiveness Report',
                'audience' => 'Ministry management', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'National standards formulated by sector; standards sales as a proxy for industry adoption; SQMT trainings delivered and participants reached; management systems certifications; product certifications including SMEs.',
                'decisions' => 'Shows how fast the standards infrastructure is expanding; supports decisions on industrial policy, SME upgrading programmes and sector prioritization for economic growth.',
                'parameters' => 'TBS: National Standards Formulated; Standards Sold; SQMT Trainings; Product Certification; Companies Certified under Management Systems.',
                'datasets' => ['TBS-NSF', 'TBS-SS', 'TBS-QCT', 'TBS-PCI', 'TBS-MSC'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'National standards formulated', 'type' => 'count', 'datasets' => ['TBS-NSF'], 'basis' => 'TBS-NSF records'],
                        ['label' => 'Standard copies sold', 'type' => 'sum', 'datasets' => ['TBS-SS'], 'field' => 'copies_sold', 'basis' => 'Σ TBS-SS.copies_sold'],
                        ['label' => 'Standards sales value', 'type' => 'sum', 'datasets' => ['TBS-SS'], 'field' => 'amount_tzs', 'format' => 'money', 'basis' => 'Σ TBS-SS.amount_tzs'],
                        ['label' => 'SQMT trainings delivered', 'type' => 'count', 'datasets' => ['TBS-QCT'], 'basis' => 'TBS-QCT records'],
                        ['label' => 'Training participants reached', 'type' => 'sum', 'datasets' => ['TBS-QCT'], 'field' => 'participants', 'basis' => 'Σ TBS-QCT.participants'],
                        ['label' => 'Product certifications', 'type' => 'count', 'datasets' => ['TBS-PCI'], 'basis' => 'TBS-PCI records'],
                        ['label' => 'SME product certifications', 'type' => 'count_where', 'datasets' => ['TBS-PCI'], 'field' => 'client_category', 'values' => ['SME', 'Small', 'Medium'], 'match' => 'contains', 'basis' => 'TBS-PCI where client is SME'],
                        ['label' => 'Management systems certifications', 'type' => 'count', 'datasets' => ['TBS-MSC'], 'basis' => 'TBS-MSC records'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Standards formulated by sector', 'dataset' => 'TBS-NSF', 'field' => 'sector'],
                    ['type' => 'breakdown', 'title' => 'Standards buyers by client category', 'dataset' => 'TBS-SS', 'field' => 'client_category'],
                    ['type' => 'trend', 'title' => 'Standards & certifications by reporting period', 'datasets' => ['TBS-NSF', 'TBS-PCI', 'TBS-MSC'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r4' => [
                'key' => 'R4', 'tier' => 1,
                'title' => 'Trade Facilitation and Import Control Dashboard',
                'audience' => 'Ministry management, TRA', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'PVoC Certificates of Conformity and Destination Inspection batch certificates issued; used motor vehicle inspections; wet cargo volumes inspected (MT); turnaround times; non-conformity rates by product and country of origin.',
                'decisions' => 'Feeds trade policy and revenue protection decisions (with TRA); detects import-risk patterns; measures how quickly compliant goods reach the market.',
                'parameters' => 'TBS: PVoC CoCs; Destination Inspection Batch Certificates; Used Motor Vehicles Inspected; Wet Cargo Inspected.',
                'datasets' => ['TBS-PVOC', 'TBS-DI', 'TBS-UMV', 'TBS-WC'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'PVoC Certificates of Conformity', 'type' => 'count', 'datasets' => ['TBS-PVOC'], 'basis' => 'TBS-PVOC records'],
                        ['label' => 'Destination Inspection certificates', 'type' => 'count', 'datasets' => ['TBS-DI'], 'basis' => 'TBS-DI records'],
                        ['label' => 'Used motor vehicles inspected', 'type' => 'count', 'datasets' => ['TBS-UMV'], 'basis' => 'TBS-UMV records'],
                        ['label' => 'Wet cargo inspected', 'type' => 'sum', 'datasets' => ['TBS-WC'], 'field' => 'quantity_mt', 'format' => 'mt', 'basis' => 'Σ TBS-WC.quantity_mt'],
                        ['label' => 'Wet cargo non-conformity rate', 'type' => 'rate_where', 'datasets' => ['TBS-WC'], 'field' => 'inspection_result', 'values' => ['Non-conforming'], 'format' => 'percent', 'basis' => 'non-conforming ÷ inspected'],
                        ['label' => 'PVoC rejected consignments', 'type' => 'count_where', 'datasets' => ['TBS-PVOC'], 'field' => 'status', 'values' => ['Rejected', 'Non-conformity'], 'match' => 'contains', 'basis' => 'TBS-PVOC rejections'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'PVoC imports by country of origin', 'dataset' => 'TBS-PVOC', 'field' => 'country_of_origin'],
                    ['type' => 'breakdown', 'title' => 'Destination Inspection by entry point', 'dataset' => 'TBS-DI', 'field' => 'entry_point'],
                    ['type' => 'breakdown', 'title' => 'Wet cargo by type', 'dataset' => 'TBS-WC', 'field' => 'cargo_type'],
                    ['type' => 'trend', 'title' => 'Import control volumes by reporting period', 'datasets' => ['TBS-PVOC', 'TBS-DI', 'TBS-UMV'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r5' => [
                'key' => 'R5', 'tier' => 1,
                'title' => 'Consumer Protection and Public Safety Report',
                'audience' => 'Ministry management', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'Food and cosmetic product and premises registrations; food risk assessments performed and risk levels; product test pass/fail rates; fuel marking volumes and sample pass rates.',
                'decisions' => 'Protects public health; triggers enforcement actions, product recalls and targeted inspections; underpins public confidence in the market.',
                'parameters' => 'TBS: Food and Cosmetic Product Registration Permits; Premises Registration Permits; Food Risk Assessments; Product Samples Tested; Fuel Marking Services.',
                'datasets' => ['TBS-FCR', 'TBS-PRP', 'TBS-FRA', 'TBS-PST', 'TBS-FM'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Food & cosmetic product permits', 'type' => 'count', 'datasets' => ['TBS-FCR'], 'basis' => 'TBS-FCR records'],
                        ['label' => 'Premises registration permits', 'type' => 'count', 'datasets' => ['TBS-PRP'], 'basis' => 'TBS-PRP records'],
                        ['label' => 'Food risk assessments', 'type' => 'count', 'datasets' => ['TBS-FRA'], 'basis' => 'TBS-FRA records'],
                        ['label' => 'High-risk findings', 'type' => 'count_where', 'datasets' => ['TBS-FRA'], 'field' => 'risk_level', 'values' => ['High'], 'match' => 'contains', 'basis' => 'TBS-FRA risk_level = High'],
                        ['label' => 'Product samples tested', 'type' => 'count', 'datasets' => ['TBS-PST'], 'basis' => 'TBS-PST records'],
                        ['label' => 'Product test pass rate', 'type' => 'rate_where', 'datasets' => ['TBS-PST'], 'field' => 'test_result', 'values' => ['Pass', 'Conforming', 'Complied'], 'match' => 'contains', 'format' => 'percent', 'basis' => 'pass ÷ tested'],
                        ['label' => 'Fuel volume marked', 'type' => 'sum', 'datasets' => ['TBS-FM'], 'field' => 'volume_marked', 'basis' => 'Σ TBS-FM.volume_marked (litres)'],
                        ['label' => 'Fuel sample pass rate', 'type' => 'ratio', 'datasets' => ['TBS-FM'], 'numerator' => 'samples_passed', 'denominator' => 'samples_taken', 'format' => 'percent', 'basis' => 'passed ÷ sampled'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Food risk assessments by risk level', 'dataset' => 'TBS-FRA', 'field' => 'risk_level'],
                    ['type' => 'breakdown', 'title' => 'Product test results', 'dataset' => 'TBS-PST', 'field' => 'test_result'],
                    ['type' => 'trend', 'title' => 'Safety services by reporting period', 'datasets' => ['TBS-FCR', 'TBS-PRP', 'TBS-PST'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r6' => [
                'key' => 'R6', 'tier' => 2,
                'title' => 'Case Management Efficiency Report (FCT)',
                'audience' => 'Institutional management', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Registry performance: cases filed vs determined, digital transmission rate, case-management system uptime, registry upgrade status, rules review progress.',
                'decisions' => 'Drives registry modernisation, digitalisation of case management and tribunal rules reform.',
                'parameters' => 'FCT: Operational Efficiency of the Case Management System; Tribunal Registry Upgraded; Tribunal Rules Reviewed; Tribunal Rules Published.',
                'datasets' => ['FCT-OE', 'FCT-RU', 'FCT-TRR', 'FCT-TRP'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Cases filed', 'type' => 'sum', 'datasets' => ['FCT-OE'], 'field' => 'cases_filed', 'basis' => 'Σ FCT-OE.cases_filed'],
                        ['label' => 'Cases transmitted digitally', 'type' => 'sum', 'datasets' => ['FCT-OE'], 'field' => 'cases_digital', 'basis' => 'Σ FCT-OE.cases_digital'],
                        ['label' => 'Digital transmission rate', 'type' => 'ratio', 'datasets' => ['FCT-OE'], 'numerator' => 'cases_digital', 'denominator' => 'cases_filed', 'format' => 'percent', 'basis' => 'digital ÷ filed'],
                        ['label' => 'Case-management system uptime', 'type' => 'avg', 'datasets' => ['FCT-OE'], 'field' => 'system_uptime', 'format' => 'percent', 'basis' => 'avg FCT-OE.system_uptime'],
                        ['label' => 'Registry upgrade components', 'type' => 'count', 'datasets' => ['FCT-RU'], 'basis' => 'FCT-RU records'],
                        ['label' => 'Tribunal rules reviewed', 'type' => 'count', 'datasets' => ['FCT-TRR'], 'basis' => 'FCT-TRR records'],
                        ['label' => 'Tribunal rules published', 'type' => 'count', 'datasets' => ['FCT-TRP'], 'basis' => 'FCT-TRP records'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Registry upgrades by status', 'dataset' => 'FCT-RU', 'field' => 'status'],
                    ['type' => 'breakdown', 'title' => 'Rules review by status', 'dataset' => 'FCT-TRR', 'field' => 'status'],
                ],
            ],

            'r7' => [
                'key' => 'R7', 'tier' => 2,
                'title' => 'Client Service Delivery Report (FCT and TBS)',
                'audience' => 'Institutional management', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Complaints received/resolved within standard timeframes; average client response days vs service standard; enquiry response performance at TBS.',
                'decisions' => 'Tracks service-charter compliance and identifies where client service is slipping.',
                'parameters' => 'FCT: Complaints Resolved within Timeframe; Average Response Time in Serving Clients. TBS: Standards Enquiries Responded.',
                'datasets' => ['FCT-CR', 'FCT-ART', 'TBS-SE'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Complaints received (FCT)', 'type' => 'sum', 'datasets' => ['FCT-CR'], 'field' => 'complaints_received', 'basis' => 'Σ FCT-CR.complaints_received'],
                        ['label' => 'Complaints resolved (FCT)', 'type' => 'sum', 'datasets' => ['FCT-CR'], 'field' => 'complaints_resolved', 'basis' => 'Σ FCT-CR.complaints_resolved'],
                        ['label' => 'Resolved within timeframe', 'type' => 'ratio', 'datasets' => ['FCT-CR'], 'numerator' => 'resolved_within_timeframe', 'denominator' => 'complaints_received', 'format' => 'percent', 'basis' => 'within timeframe ÷ received'],
                        ['label' => 'Avg client response (FCT)', 'type' => 'avg', 'datasets' => ['FCT-ART'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg FCT-ART.average_response_days'],
                        ['label' => 'Enquiries received (TBS)', 'type' => 'sum', 'datasets' => ['TBS-SE'], 'field' => 'enquiries_received', 'basis' => 'Σ TBS-SE.enquiries_received'],
                        ['label' => 'Enquiries responded (TBS)', 'type' => 'sum', 'datasets' => ['TBS-SE'], 'field' => 'enquiries_responded', 'basis' => 'Σ TBS-SE.enquiries_responded'],
                        ['label' => 'Enquiry response rate (TBS)', 'type' => 'ratio', 'datasets' => ['TBS-SE'], 'numerator' => 'enquiries_responded', 'denominator' => 'enquiries_received', 'format' => 'percent', 'basis' => 'responded ÷ received'],
                        ['label' => 'Avg enquiry response (TBS)', 'type' => 'avg', 'datasets' => ['TBS-SE'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg TBS-SE.average_response_days'],
                    ]],
                    ['type' => 'trend', 'title' => 'Response performance by reporting period', 'datasets' => ['FCT-ART', 'TBS-SE'], 'measure' => ['type' => 'avg', 'field' => 'average_response_days'], 'unit' => 'days'],
                ],
            ],

            'r8' => [
                'key' => 'R8', 'tier' => 2,
                'title' => 'Laboratory, Metrology and Certification Output Report (TBS)',
                'audience' => 'Institutional management', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Volumes and turnaround of product tests, equipment calibrations (with next-due-date tracking), product certifications and management-system certifications; SME share of certifications.',
                'decisions' => 'Balances laboratory capacity, calibration schedules and certification pipelines; tracks SME upgrading.',
                'parameters' => 'TBS: Product Samples Tested; Equipment/Items Calibrated; Product Certification; Companies Certified under Management Systems.',
                'datasets' => ['TBS-PST', 'TBS-EC', 'TBS-PCI', 'TBS-MSC'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Product samples tested', 'type' => 'count', 'datasets' => ['TBS-PST'], 'basis' => 'TBS-PST records'],
                        ['label' => 'Test pass rate', 'type' => 'rate_where', 'datasets' => ['TBS-PST'], 'field' => 'test_result', 'values' => ['Pass', 'Conforming', 'Complied'], 'match' => 'contains', 'format' => 'percent', 'basis' => 'pass ÷ tested'],
                        ['label' => 'Equipment/items calibrated', 'type' => 'count', 'datasets' => ['TBS-EC'], 'basis' => 'TBS-EC records'],
                        ['label' => 'Calibrations due within 90 days', 'type' => 'due_within', 'datasets' => ['TBS-EC'], 'field' => 'next_due_date', 'days' => 90, 'basis' => 'TBS-EC.next_due_date ≤ today + 90d'],
                        ['label' => 'Product certifications', 'type' => 'count', 'datasets' => ['TBS-PCI'], 'basis' => 'TBS-PCI records'],
                        ['label' => 'SME share of certifications', 'type' => 'rate_where', 'datasets' => ['TBS-PCI'], 'field' => 'client_category', 'values' => ['SME', 'Small', 'Medium'], 'match' => 'contains', 'format' => 'percent', 'basis' => 'SME ÷ all certifications'],
                        ['label' => 'Management systems certifications', 'type' => 'count', 'datasets' => ['TBS-MSC'], 'basis' => 'TBS-MSC records'],
                        ['label' => 'Certificates expiring within 90 days', 'type' => 'due_within', 'datasets' => ['TBS-MSC'], 'field' => 'validity_date', 'days' => 90, 'basis' => 'TBS-MSC.validity_date ≤ today + 90d'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Tests by type', 'dataset' => 'TBS-PST', 'field' => 'test_type'],
                    ['type' => 'breakdown', 'title' => 'Certified management systems', 'dataset' => 'TBS-MSC', 'field' => 'management_system'],
                    ['type' => 'trend', 'title' => 'Laboratory & certification output by period', 'datasets' => ['TBS-PST', 'TBS-EC', 'TBS-PCI'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r9' => [
                'key' => 'R9', 'tier' => 2,
                'title' => 'Stakeholder Engagement and Awareness Report',
                'audience' => 'Institutional management', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Consultative meetings, public awareness activities, trainings and exporter assistance delivered, with reach (participants, regions) and key outputs.',
                'decisions' => 'Plans outreach coverage, measures stakeholder reach and prioritises exporter support.',
                'parameters' => 'FCT: Consultative Meetings with Stakeholders; Public Awareness Conducted. TBS: SQMT Trainings; Technical Assistance to Exporters.',
                'datasets' => ['FCT-CM', 'FCT-PA', 'TBS-QCT', 'TBS-TAE'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Headline indicators', 'items' => [
                        ['label' => 'Consultative meetings held', 'type' => 'count', 'datasets' => ['FCT-CM'], 'basis' => 'FCT-CM records'],
                        ['label' => 'Meeting participants', 'type' => 'sum', 'datasets' => ['FCT-CM'], 'field' => 'participants', 'basis' => 'Σ FCT-CM.participants'],
                        ['label' => 'Public awareness activities', 'type' => 'count', 'datasets' => ['FCT-PA'], 'basis' => 'FCT-PA records'],
                        ['label' => 'Awareness participants reached', 'type' => 'sum', 'datasets' => ['FCT-PA'], 'field' => 'participants', 'basis' => 'Σ FCT-PA.participants'],
                        ['label' => 'Regions reached', 'type' => 'distinct', 'datasets' => ['FCT-PA'], 'field' => 'venue_region', 'basis' => 'distinct FCT-PA.venue_region'],
                        ['label' => 'SQMT trainings delivered', 'type' => 'count', 'datasets' => ['TBS-QCT'], 'basis' => 'TBS-QCT records'],
                        ['label' => 'Exporter assistances provided', 'type' => 'count', 'datasets' => ['TBS-TAE'], 'basis' => 'TBS-TAE records'],
                    ]],
                    ['type' => 'breakdown', 'title' => 'Meetings by stakeholder category', 'dataset' => 'FCT-CM', 'field' => 'stakeholder_category'],
                    ['type' => 'breakdown', 'title' => 'Exporter assistance by type', 'dataset' => 'TBS-TAE', 'field' => 'assistance_type'],
                ],
            ],

            'r10' => [
                'key' => 'R10', 'tier' => 2,
                'title' => 'Data Submission Compliance Report (All Institutions)',
                'audience' => 'Institutions, Ministry ICT', 'frequency' => 'Quarterly', 'priority' => 'High',
                'summary' => 'Per institution: submissions received vs expected per reporting calendar, first-pass validation rate, returns for rectification, timeliness against deadlines.',
                'decisions' => 'Enforces the reporting calendar; targets support to institutions that fall behind or submit poor-quality data.',
                'parameters' => 'Viwanda Portal submission and approval workflow records (all data parameters).',
                'datasets' => [],
                'sections' => [
                    ['type' => 'submission_compliance', 'title' => 'Reporting calendar compliance by institution'],
                ],
            ],

            'r11' => [
                'key' => 'R11', 'tier' => 3,
                'title' => 'Quarterly Service Delivery Bulletin',
                'audience' => 'Public', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Plain-language summary: permits, certificates, tests, trainings and cases handled per quarter, with trends. Publishable on the Viwanda Portal and institution websites.',
                'decisions' => 'Public accountability — publishing service volumes and turnaround times creates pressure for continuous institutional improvement.',
                'parameters' => 'All approved FCT and TBS service-volume parameters (published data only).',
                'datasets' => ['FCT-AC', 'FCT-CA', 'FCT-CR', 'TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-UMV', 'TBS-PCI', 'TBS-MSC', 'TBS-PST', 'TBS-EC', 'TBS-QCT', 'TBS-NSF', 'TBS-FM'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Services delivered this period', 'items' => [
                        ['label' => 'Cases handled (FCT)', 'type' => 'count', 'datasets' => ['FCT-AC', 'FCT-CA'], 'basis' => 'FCT-AC + FCT-CA records'],
                        ['label' => 'Complaints resolved (FCT)', 'type' => 'sum', 'datasets' => ['FCT-CR'], 'field' => 'complaints_resolved', 'basis' => 'Σ FCT-CR.complaints_resolved'],
                        ['label' => 'Permits & certificates issued', 'type' => 'count', 'datasets' => ['TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-UMV', 'TBS-PCI', 'TBS-MSC'], 'basis' => 'all TBS permit/certificate records'],
                        ['label' => 'Product tests performed', 'type' => 'count', 'datasets' => ['TBS-PST'], 'basis' => 'TBS-PST records'],
                        ['label' => 'Equipment calibrated', 'type' => 'count', 'datasets' => ['TBS-EC'], 'basis' => 'TBS-EC records'],
                        ['label' => 'Trainings delivered', 'type' => 'count', 'datasets' => ['TBS-QCT'], 'basis' => 'TBS-QCT records'],
                        ['label' => 'People trained', 'type' => 'sum', 'datasets' => ['TBS-QCT'], 'field' => 'participants', 'basis' => 'Σ TBS-QCT.participants'],
                        ['label' => 'National standards published', 'type' => 'count', 'datasets' => ['TBS-NSF'], 'basis' => 'TBS-NSF records'],
                    ]],
                    ['type' => 'trend', 'title' => 'Total services delivered by reporting period', 'datasets' => ['FCT-AC', 'FCT-CA', 'TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-UMV', 'TBS-PCI', 'TBS-MSC', 'TBS-PST', 'TBS-EC', 'TBS-QCT', 'TBS-NSF'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r12' => [
                'key' => 'R12', 'tier' => 3,
                'title' => 'Annual State of Standards, Quality and Fair Competition Report',
                'audience' => 'Public, investors', 'frequency' => 'Annual', 'priority' => 'Medium',
                'summary' => 'Flagship annual publication combining the Tier 1 reports: standards development progress, market fairness trends, consumer safety results and trade facilitation outcomes, with policy recommendations.',
                'decisions' => 'Whole-year accountability and policy communication; select a year to compile the annual picture.',
                'parameters' => 'Combines R1–R5 indicators for the selected year (published data only).',
                'datasets' => ['FCT-RC', 'FCT-AC', 'FCT-ES', 'TBS-NSF', 'TBS-PCI', 'TBS-MSC', 'TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI', 'TBS-WC', 'TBS-PST', 'TBS-FRA', 'TBS-FM'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Annual headline indicators', 'items' => [
                        ['label' => 'Case resolution rate (FCT)', 'type' => 'ratio', 'datasets' => ['FCT-RC'], 'numerator' => 'cases_resolved', 'denominator' => 'cases_due', 'format' => 'percent', 'basis' => 'resolved ÷ due'],
                        ['label' => 'Competition cases filed', 'type' => 'count', 'datasets' => ['FCT-AC'], 'basis' => 'FCT-AC records'],
                        ['label' => 'Economic studies completed', 'type' => 'count', 'datasets' => ['FCT-ES'], 'basis' => 'FCT-ES records'],
                        ['label' => 'National standards formulated', 'type' => 'count', 'datasets' => ['TBS-NSF'], 'basis' => 'TBS-NSF records'],
                        ['label' => 'Certifications issued', 'type' => 'count', 'datasets' => ['TBS-PCI', 'TBS-MSC'], 'basis' => 'TBS-PCI + TBS-MSC'],
                        ['label' => 'Permits issued', 'type' => 'count', 'datasets' => ['TBS-FCR', 'TBS-PRP', 'TBS-PVOC', 'TBS-DI'], 'basis' => 'FCR + PRP + PVoC + DI'],
                        ['label' => 'Wet cargo inspected', 'type' => 'sum', 'datasets' => ['TBS-WC'], 'field' => 'quantity_mt', 'format' => 'mt', 'basis' => 'Σ TBS-WC.quantity_mt'],
                        ['label' => 'Product test pass rate', 'type' => 'rate_where', 'datasets' => ['TBS-PST'], 'field' => 'test_result', 'values' => ['Pass', 'Conforming', 'Complied'], 'match' => 'contains', 'format' => 'percent', 'basis' => 'pass ÷ tested'],
                        ['label' => 'High food-safety risks flagged', 'type' => 'count_where', 'datasets' => ['TBS-FRA'], 'field' => 'risk_level', 'values' => ['High'], 'match' => 'contains', 'basis' => 'TBS-FRA risk_level = High'],
                        ['label' => 'Fuel sample pass rate', 'type' => 'ratio', 'datasets' => ['TBS-FM'], 'numerator' => 'samples_passed', 'denominator' => 'samples_taken', 'format' => 'percent', 'basis' => 'passed ÷ sampled'],
                    ]],
                    ['type' => 'trend', 'title' => 'Quarterly build-up within the year', 'datasets' => ['FCT-AC', 'TBS-NSF', 'TBS-PCI', 'TBS-PVOC', 'TBS-FCR'], 'measure' => ['type' => 'count']],
                ],
            ],

            'r13' => [
                'key' => 'R13', 'tier' => 3,
                'title' => 'Public Performance Dashboards',
                'audience' => 'Public', 'frequency' => 'Quarterly', 'priority' => 'Medium',
                'summary' => 'Interactive dashboards on approved (Ministry-validated) data: service volumes, turnaround times, resolution rates, certified SMEs count, fuel marking coverage and food safety risk alerts.',
                'decisions' => 'Transparency instrument — the public and investors track government service performance in near-real time.',
                'parameters' => 'Published records from FCT-RC, FCT-ART, TBS-SE, TBS-PCI, TBS-FM, TBS-FRA and all service-volume datasets.',
                'datasets' => ['FCT-RC', 'FCT-ART', 'TBS-SE', 'TBS-PCI', 'TBS-FM', 'TBS-FRA', 'TBS-PST', 'TBS-PVOC', 'TBS-FCR'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Current performance', 'items' => [
                        ['label' => 'Case resolution rate', 'type' => 'ratio', 'datasets' => ['FCT-RC'], 'numerator' => 'cases_resolved', 'denominator' => 'cases_due', 'format' => 'percent', 'basis' => 'resolved ÷ due'],
                        ['label' => 'Avg client response (FCT)', 'type' => 'avg', 'datasets' => ['FCT-ART'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg FCT-ART.average_response_days'],
                        ['label' => 'Avg enquiry response (TBS)', 'type' => 'avg', 'datasets' => ['TBS-SE'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg TBS-SE.average_response_days'],
                        ['label' => 'Certified SMEs', 'type' => 'count_where', 'datasets' => ['TBS-PCI'], 'field' => 'client_category', 'values' => ['SME', 'Small', 'Medium'], 'match' => 'contains', 'basis' => 'TBS-PCI where client is SME'],
                        ['label' => 'Fuel sample pass rate', 'type' => 'ratio', 'datasets' => ['TBS-FM'], 'numerator' => 'samples_passed', 'denominator' => 'samples_taken', 'format' => 'percent', 'basis' => 'passed ÷ sampled'],
                        ['label' => 'Food safety risk alerts', 'type' => 'count_where', 'datasets' => ['TBS-FRA'], 'field' => 'risk_level', 'values' => ['High'], 'match' => 'contains', 'basis' => 'TBS-FRA risk_level = High'],
                    ]],
                    ['type' => 'trend', 'title' => 'Service volumes by reporting period', 'datasets' => ['TBS-PST', 'TBS-PVOC', 'TBS-FCR', 'TBS-PCI'], 'measure' => ['type' => 'count']],
                    ['type' => 'trend', 'title' => 'Turnaround times by reporting period', 'datasets' => ['FCT-ART', 'TBS-SE'], 'measure' => ['type' => 'avg', 'field' => 'average_response_days'], 'unit' => 'days'],
                    ['type' => 'breakdown', 'title' => 'Food safety risk alerts by level', 'dataset' => 'TBS-FRA', 'field' => 'risk_level'],
                ],
            ],

            'r14' => [
                'key' => 'R14', 'tier' => 3,
                'title' => 'Investor and Business Community Brief',
                'audience' => 'Investors, business community', 'frequency' => 'Semi-annual', 'priority' => 'Medium',
                'summary' => 'Semi-annual brief on regulatory certainty indicators: case disposal times, PVoC/CoC turnaround, certification lead times and standards availability — the practical cost-of-compliance picture for investors.',
                'decisions' => 'Investor confidence — a predictable, measurable regulatory environment lowers the perceived cost of doing business.',
                'parameters' => 'FCT: Average Disposal Time; Average Client Response Time. TBS: PVoC CoCs; Destination Inspection; Product Certification; National Standards Formulated; Standards Enquiries Responded.',
                'datasets' => ['FCT-ATD', 'FCT-ART', 'TBS-PVOC', 'TBS-DI', 'TBS-PCI', 'TBS-NSF', 'TBS-SE'],
                'sections' => [
                    ['type' => 'kpis', 'title' => 'Regulatory certainty indicators', 'items' => [
                        ['label' => 'Average case disposal time', 'type' => 'avg', 'datasets' => ['FCT-ATD'], 'field' => 'average_months', 'format' => 'months', 'basis' => 'avg FCT-ATD.average_months'],
                        ['label' => 'Average client response (FCT)', 'type' => 'avg', 'datasets' => ['FCT-ART'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg FCT-ART.average_response_days'],
                        ['label' => 'Average enquiry response (TBS)', 'type' => 'avg', 'datasets' => ['TBS-SE'], 'field' => 'average_response_days', 'format' => 'days', 'basis' => 'avg TBS-SE.average_response_days'],
                        ['label' => 'PVoC CoCs issued', 'type' => 'count', 'datasets' => ['TBS-PVOC'], 'basis' => 'TBS-PVOC records'],
                        ['label' => 'Destination Inspection certificates', 'type' => 'count', 'datasets' => ['TBS-DI'], 'basis' => 'TBS-DI records'],
                        ['label' => 'Product certifications issued', 'type' => 'count', 'datasets' => ['TBS-PCI'], 'basis' => 'TBS-PCI records'],
                        ['label' => 'National standards available', 'type' => 'count', 'datasets' => ['TBS-NSF'], 'basis' => 'TBS-NSF records'],
                    ]],
                    ['type' => 'trend', 'title' => 'Import facilitation volumes by period', 'datasets' => ['TBS-PVOC', 'TBS-DI'], 'measure' => ['type' => 'count']],
                ],
            ],
'n1' => [
                'key' => 'N1',
                'tier' => 4,
                'title' => 'National Industrial Production, Capacity and Employment',
                'audience' => 'Ministry management, PO-PSM',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'National industrial scorecard from the Department of Industrial Development: installed capacity vs actual production, demand coverage, direct and indirect employment by industry type, closed industries, industrial licensing and KAIZEN/industrial development initiatives.',
                'decisions' => 'Sizing the industrial base for national growth; identifying idle capacity and dead industries for revival; targeting licensing, KAIZEN and investor-support interventions.',
                'parameters' => 'DID: Industrial capacity/production/demand/employment by industry type (incl. closed industries); Industrial licensing register; Industrial development initiatives. SIDO: establishments & employment.',
                'datasets' => [
                    'MIT-IND-CAP',
                    'MIT-IND-DEV',
                    'MIT-ILR',
                    'SIDO-IE'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Installed production capacity',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'field' => 'production_capacity',
                                'format' => 'number',
                                'basis' => 'Σ MIT-IND-CAP.production_capacity'
                            ],
                            [
                                'label' => 'Actual production',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'field' => 'actual_production',
                                'format' => 'number',
                                'basis' => 'Σ MIT-IND-CAP.actual_production'
                            ],
                            [
                                'label' => 'Capacity utilisation',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'numerator' => 'actual_production',
                                'denominator' => 'production_capacity',
                                'format' => 'percent',
                                'basis' => 'actual production ÷ capacity'
                            ],
                            [
                                'label' => 'Demand coverage',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'numerator' => 'actual_production',
                                'denominator' => 'actual_demand',
                                'format' => 'percent',
                                'basis' => 'actual production ÷ real demand'
                            ],
                            [
                                'label' => 'Direct jobs in industry',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'field' => 'direct_jobs',
                                'basis' => 'Σ direct jobs'
                            ],
                            [
                                'label' => 'Indirect jobs',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'field' => 'indirect_jobs',
                                'basis' => 'Σ indirect jobs'
                            ],
                            [
                                'label' => 'Closed industries',
                                'type' => 'count_where',
                                'datasets' => [
                                    'MIT-IND-CAP'
                                ],
                                'field' => 'status',
                                'values' => [
                                    'Closed'
                                ],
                                'match' => 'exact',
                                'basis' => 'MIT-IND-CAP status = Closed'
                            ],
                            [
                                'label' => 'New industrial licences',
                                'type' => 'count_where',
                                'datasets' => [
                                    'MIT-ILR'
                                ],
                                'field' => 'status',
                                'values' => [
                                    'New'
                                ],
                                'match' => 'exact',
                                'basis' => 'MIT-ILR status = New'
                            ],
                            [
                                'label' => 'KAIZEN interventions',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-IND-DEV'
                                ],
                                'field' => 'kaizen_interventions',
                                'basis' => 'Σ MIT-IND-DEV.kaizen_interventions'
                            ],
                            [
                                'label' => 'SME establishments (SIDO)',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-IE'
                                ],
                                'field' => 'establishments',
                                'basis' => 'Σ SIDO-IE.establishments'
                            ]
                        ]
                    ],
                    [
                        'type' => 'breakdown',
                        'title' => 'Production by industry type',
                        'dataset' => 'MIT-IND-CAP',
                        'field' => 'industry_type'
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Actual production by reporting period',
                        'datasets' => [
                            'MIT-IND-CAP'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'actual_production'
                        ]
                    ]
                ]
            ],
'n2' => [
                'key' => 'N2',
                'tier' => 4,
                'title' => 'National Trade Performance and Market Access',
                'audience' => 'Ministry management, TRA, BoT',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'Merchandise trade with the World, EU, EAC, SADC, Asia, China, India and Japan (exports vs imports), market-access negotiations, NTB elimination, trade missions and agreements, plus export-promotion results and border clearance times.',
                'decisions' => 'Guides trade negotiations and market-access strategy; tracks the trade balance trajectory and the payoff from trade missions and export promotion.',
                'parameters' => 'DTI: Merchandise trade by market (exports/imports USD); Trade integration & market access. TRD: Trade facilitation statistics; Export promotion activities.',
                'datasets' => [
                    'MIT-DTI-TRADE',
                    'MIT-DTI-ACCESS',
                    'MIT-TFS',
                    'MIT-EPA'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Total exports (USD m)',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-TRADE'
                                ],
                                'field' => 'exports_usd',
                                'format' => 'usd',
                                'basis' => 'Σ exports across markets'
                            ],
                            [
                                'label' => 'Total imports (USD m)',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-TRADE'
                                ],
                                'field' => 'imports_usd',
                                'format' => 'usd',
                                'basis' => 'Σ imports across markets'
                            ],
                            [
                                'label' => 'Export–import cover',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-DTI-TRADE'
                                ],
                                'numerator' => 'exports_usd',
                                'denominator' => 'imports_usd',
                                'format' => 'percent',
                                'basis' => 'exports ÷ imports'
                            ],
                            [
                                'label' => 'Negotiations participated',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-ACCESS'
                                ],
                                'field' => 'negotiations_participated',
                                'basis' => 'Σ negotiations (EAC, AfCFTA, TFTA, SADC, AGOA)'
                            ],
                            [
                                'label' => 'NTBs addressed',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-ACCESS'
                                ],
                                'field' => 'ntbs_addressed',
                                'basis' => 'Σ NTBs addressed/eliminated'
                            ],
                            [
                                'label' => 'Trade missions & forums',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-ACCESS'
                                ],
                                'field' => 'trade_missions',
                                'basis' => 'Σ trade missions'
                            ],
                            [
                                'label' => 'Agreements concluded',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTI-ACCESS'
                                ],
                                'field' => 'agreements_concluded',
                                'basis' => 'Σ MoUs / trade agreements'
                            ],
                            [
                                'label' => 'Export deals via promotion (USD m)',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-EPA'
                                ],
                                'field' => 'export_deals_value_musd',
                                'basis' => 'Σ MIT-EPA.export_deals_value_musd'
                            ]
                        ]
                    ],
                    [
                        'type' => 'breakdown',
                        'title' => 'Trade records by market',
                        'dataset' => 'MIT-DTI-TRADE',
                        'field' => 'market'
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Exports by reporting period (USD m)',
                        'datasets' => [
                            'MIT-DTI-TRADE'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'exports_usd'
                        ]
                    ]
                ]
            ],
'n3' => [
                'key' => 'N3',
                'tier' => 4,
                'title' => 'Business Registration and Formalisation',
                'audience' => 'Ministry management',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'Business entry and formalisation indicators from BRELA and the Department of Trade and Development: new companies and business names, industrial licences, Class A licences, certificates, trademarks, patents, beneficial ownership and formalized informal enterprises.',
                'decisions' => 'Tracks private-sector growth and formalisation — leading indicators of investment, tax-base expansion and job creation.',
                'parameters' => 'BRELA: registration & licensing indicators. DTD: formalization & licence inspections. IND: industrial licensing register.',
                'datasets' => [
                    'BRELA-REG',
                    'MIT-DTD-PROM',
                    'MIT-ILR'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'New companies registered',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'new_companies_registered',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Companies closed',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'companies_closed',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'New business names',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'new_business_names',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Industrial licences issued',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'industrial_licences_issued',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Class "A" business licences',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'class_a_business_licences',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Trademarks registered',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'trademarks_registered',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Patents granted',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'patents_granted',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Beneficial ownership records',
                                'type' => 'sum',
                                'datasets' => [
                                    'BRELA-REG'
                                ],
                                'field' => 'beneficial_ownership_records',
                                'basis' => 'Σ BRELA-REG'
                            ],
                            [
                                'label' => 'Informal enterprises formalized',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTD-PROM'
                                ],
                                'field' => 'formalized_enterprises',
                                'basis' => 'Σ MIT-DTD-PROM'
                            ]
                        ]
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'New company registrations by period',
                        'datasets' => [
                            'BRELA-REG'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'new_companies_registered'
                        ]
                    ]
                ]
            ],
'n4' => [
                'key' => 'N4',
                'tier' => 4,
                'title' => 'SME Development and Access to Finance',
                'audience' => 'Ministry management, SIDO, SME Dept',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'SME growth and financing: SIDO credit facilitation (startups, innovations, youth and small businesses financed, loan values, NPL), new small/medium industries, skills training and jobs, plus SME Department formalization, clusters and market linkages.',
                'decisions' => 'Directs SME policy and credit schemes; flags portfolio risk (NPL) and measures employment created through SME support.',
                'parameters' => 'SIDO: SME credit, skills & entrepreneurship; establishments & employment. SME Dept: development & formalisation.',
                'datasets' => [
                    'SIDO-CRD',
                    'SIDO-IE',
                    'MIT-SME-DEV'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Loans value (TZS bn)',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'loans_value_tzs_bn',
                                'basis' => 'Σ SIDO-CRD.loans_value_tzs_bn'
                            ],
                            [
                                'label' => 'Start-ups financed',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'startups_loaned',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'Small businesses financed',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'small_businesses_loaned',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'Youth financed',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'youth_loaned',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'Portfolio NPL',
                                'type' => 'avg',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'npl_percent',
                                'format' => 'percent',
                                'basis' => 'avg SIDO-CRD.npl_percent'
                            ],
                            [
                                'label' => 'New small/medium industries',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'new_small_medium_industries',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'Youth trained in skills',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'youth_skills_trained',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'New direct jobs',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'new_direct_jobs',
                                'basis' => 'Σ SIDO-CRD'
                            ],
                            [
                                'label' => 'SME clusters supported',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-SME-DEV'
                                ],
                                'field' => 'clusters_supported',
                                'basis' => 'Σ MIT-SME-DEV'
                            ],
                            [
                                'label' => 'SMEs linked to markets',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-SME-DEV'
                                ],
                                'field' => 'smes_linked_to_markets',
                                'basis' => 'Σ MIT-SME-DEV'
                            ]
                        ]
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Jobs created through SME support by period',
                        'datasets' => [
                            'SIDO-CRD'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'new_direct_jobs'
                        ]
                    ]
                ]
            ],
'n5' => [
                'key' => 'N5',
                'tier' => 4,
                'title' => 'Legal Metrology and Fair Trade',
                'audience' => 'Ministry management, WMA',
                'frequency' => 'Quarterly',
                'priority' => 'Medium',
                'summary' => 'Weights and Measures Agency performance: instruments verified, standards calibrated, pattern approvals, pre-packed goods and border compliance inspections, prosecutions, offences compounded and stakeholder awareness.',
                'decisions' => 'Protects consumers and fair trade transactions; monitors enforcement coverage across regions and border posts.',
                'parameters' => 'WMA: verification, calibration, inspection, enforcement and awareness indicators.',
                'datasets' => [
                    'WMA-LM'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Measuring instruments verified',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'instruments_verified',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Secondary standards calibrated',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'secondary_standards_calibrated',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'New patterns approved',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'new_patterns_approved',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Pre-packed goods inspections',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'prepacked_goods_inspections',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Routine & surprise inspections',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'routine_surprise_inspections',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Border compliance verifications',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'border_compliance_verifications',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Cases prosecuted',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'cases_prosecuted',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Offences compounded',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'offences_compounded',
                                'basis' => 'Σ WMA-LM'
                            ],
                            [
                                'label' => 'Awareness seminars',
                                'type' => 'sum',
                                'datasets' => [
                                    'WMA-LM'
                                ],
                                'field' => 'awareness_seminars',
                                'basis' => 'Σ WMA-LM'
                            ]
                        ]
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Instruments verified by period',
                        'datasets' => [
                            'WMA-LM'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'instruments_verified'
                        ]
                    ]
                ]
            ],
'n6' => [
                'key' => 'N6',
                'tier' => 4,
                'title' => 'Competition Enforcement and Consumer Redress',
                'audience' => 'Ministry management, FCC, FCT',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'Fair Competition Commission enforcement pipeline: enforcement actions and final findings, complaints investigated vs resolved, mergers and exemptions, standard-form consumer contracts, price/market surveillance, raids, seizures and counterfeit disposals.',
                'decisions' => 'Measures market fairness enforcement intensity and consumer redress; informs competition policy and enforcement resourcing.',
                'parameters' => 'FCC: competition enforcement & consumer protection indicators; complaints & notifications register.',
                'datasets' => [
                    'FCC-CPE',
                    'FCC-CN'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Enforcement actions',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'enforcement_actions',
                                'basis' => 'Σ FCC-CPE'
                            ],
                            [
                                'label' => 'Complaints investigated',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'complaints_investigated',
                                'basis' => 'Σ FCC-CPE'
                            ],
                            [
                                'label' => 'Complaint resolution rate',
                                'type' => 'ratio',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'numerator' => 'complaints_resolved',
                                'denominator' => 'complaints_investigated',
                                'format' => 'percent',
                                'basis' => 'resolved ÷ investigated'
                            ],
                            [
                                'label' => 'Mergers investigated',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'mergers_investigated',
                                'basis' => 'Σ FCC-CPE'
                            ],
                            [
                                'label' => 'SFCC certificates approved',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'sfcc_approved',
                                'basis' => 'Σ FCC-CPE'
                            ],
                            [
                                'label' => 'Surveillance activities',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'market_surveillance',
                                'basis' => 'Σ market surveillance'
                            ],
                            [
                                'label' => 'Raids & seizures',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'seizures',
                                'basis' => 'Σ seizures at ports/ICDs'
                            ],
                            [
                                'label' => 'Counterfeit disposals',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'counterfeit_disposals',
                                'basis' => 'Σ disposal exercises'
                            ],
                            [
                                'label' => 'Research studies completed',
                                'type' => 'sum',
                                'datasets' => [
                                    'FCC-CPE'
                                ],
                                'field' => 'research_studies',
                                'basis' => 'Σ FCC-CPE'
                            ]
                        ]
                    ],
                    [
                        'type' => 'breakdown',
                        'title' => 'Matters by type',
                        'dataset' => 'FCC-CN',
                        'field' => 'matter_type'
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Enforcement actions by period',
                        'datasets' => [
                            'FCC-CPE'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'enforcement_actions'
                        ]
                    ]
                ]
            ],
'n7' => [
                'key' => 'N7',
                'tier' => 4,
                'title' => 'Industrial Projects and Technology Development',
                'audience' => 'Ministry management, NDC, TEMDO, CAMARTEC, TIRDO',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'Strategic industrial projects portfolio (Engaruka, Mchuchuma–Liganga, coal, KMTC, Nyanza Glass, rubber estates) with milestones and funds mobilised, plus technology development output from TEMDO, CAMARTEC and TIRDO.',
                'decisions' => 'Tracks delivery of the strategic industrial projects pipeline and the technology/R&D base that feeds industrialisation; flags delayed or at-risk projects.',
                'parameters' => 'NDC: projects portfolio & industrial operations. TEMDO: engineering design & technology. CAMARTEC: agricultural & rural technologies. TIRDO: industrial research & innovation.',
                'datasets' => [
                    'NDC-PROJ',
                    'NDC-OPS',
                    'TEMDO-ENG',
                    'CAMARTEC-AGT',
                    'TIRDO-RDI'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Strategic projects tracked',
                                'type' => 'distinct',
                                'datasets' => [
                                    'NDC-PROJ'
                                ],
                                'field' => 'project',
                                'basis' => 'distinct NDC-PROJ projects'
                            ],
                            [
                                'label' => 'Projects on track',
                                'type' => 'count_where',
                                'datasets' => [
                                    'NDC-PROJ'
                                ],
                                'field' => 'status',
                                'values' => [
                                    'On track'
                                ],
                                'match' => 'exact',
                                'basis' => 'NDC-PROJ status = On track'
                            ],
                            [
                                'label' => 'Project funds mobilised',
                                'type' => 'sum',
                                'datasets' => [
                                    'NDC-PROJ'
                                ],
                                'field' => 'funds_mobilised_tzs',
                                'format' => 'money',
                                'basis' => 'Σ NDC-PROJ.funds_mobilised_tzs'
                            ],
                            [
                                'label' => 'Machines produced (NDC)',
                                'type' => 'sum',
                                'datasets' => [
                                    'NDC-OPS'
                                ],
                                'field' => 'machines_produced',
                                'basis' => 'Σ NDC-OPS'
                            ],
                            [
                                'label' => 'Prototypes developed (TEMDO)',
                                'type' => 'sum',
                                'datasets' => [
                                    'TEMDO-ENG'
                                ],
                                'field' => 'prototypes_developed',
                                'basis' => 'Σ TEMDO-ENG'
                            ],
                            [
                                'label' => 'Machines & technologies deployed',
                                'type' => 'sum',
                                'datasets' => [
                                    'TEMDO-ENG'
                                ],
                                'field' => 'machines_deployed',
                                'basis' => 'Σ TEMDO-ENG'
                            ],
                            [
                                'label' => 'Agri technologies fabricated',
                                'type' => 'sum',
                                'datasets' => [
                                    'CAMARTEC-AGT'
                                ],
                                'field' => 'technologies_fabricated',
                                'basis' => 'Σ CAMARTEC-AGT'
                            ],
                            [
                                'label' => 'R&D partnerships (TIRDO)',
                                'type' => 'sum',
                                'datasets' => [
                                    'TIRDO-RDI'
                                ],
                                'field' => 'partnerships_established',
                                'basis' => 'Σ TIRDO-RDI'
                            ]
                        ]
                    ],
                    [
                        'type' => 'breakdown',
                        'title' => 'Portfolio by project',
                        'dataset' => 'NDC-PROJ',
                        'field' => 'project'
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Portfolio activity by period',
                        'datasets' => [
                            'NDC-PROJ'
                        ],
                        'measure' => [
                            'type' => 'count'
                        ]
                    ]
                ]
            ],
'n8' => [
                'key' => 'N8',
                'tier' => 4,
                'title' => 'Skills Development, Research and Innovation',
                'audience' => 'Ministry management, CBE, TIRDO',
                'frequency' => 'Quarterly',
                'priority' => 'Medium',
                'summary' => 'Human-capital and innovation pipeline: CBE enrolment, field/industrial training, academic programmes, tracer studies, research output (projects, publications, funds), consultancy and incubation; TIRDO training and industrial linkages; SIDO skills training.',
                'decisions' => 'Links education and research output to industrial skills needs; tracks research commercialisation and youth skills formation.',
                'parameters' => 'CBE: skills, research & innovation indicators. TIRDO: research & innovation. SIDO: skills training.',
                'datasets' => [
                    'CBE-EDU',
                    'TIRDO-RDI',
                    'SIDO-CRD'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Students enrolled (CBE)',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'students_enrolled',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Students in field/industrial training',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'students_field_supervised',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'New academic programmes',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'new_programmes',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Publications — local',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'publications_local',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Publications — international',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'publications_international',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Research funds solicited',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'research_funds_tzs',
                                'format' => 'money',
                                'basis' => 'Σ CBE-EDU.research_funds_tzs'
                            ],
                            [
                                'label' => 'Consultancies carried out',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'consultancies',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Incubation programmes',
                                'type' => 'sum',
                                'datasets' => [
                                    'CBE-EDU'
                                ],
                                'field' => 'incubation_programmes',
                                'basis' => 'Σ CBE-EDU'
                            ],
                            [
                                'label' => 'Industrial visits (TIRDO)',
                                'type' => 'sum',
                                'datasets' => [
                                    'TIRDO-RDI'
                                ],
                                'field' => 'industrial_visits',
                                'basis' => 'Σ TIRDO-RDI'
                            ],
                            [
                                'label' => 'Youth trained in skills (SIDO)',
                                'type' => 'sum',
                                'datasets' => [
                                    'SIDO-CRD'
                                ],
                                'field' => 'youth_skills_trained',
                                'basis' => 'Σ SIDO-CRD'
                            ]
                        ]
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Enrolment by period',
                        'datasets' => [
                            'CBE-EDU'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'students_enrolled'
                        ]
                    ]
                ]
            ],
'n9' => [
                'key' => 'N9',
                'tier' => 4,
                'title' => 'Trade Promotion and Market Linkages',
                'audience' => 'Ministry management, TANTRADE, DTD',
                'frequency' => 'Quarterly',
                'priority' => 'Medium',
                'summary' => 'Trade promotion machinery: value-chain assessments, market information products, SME training by gender, coaching and business clinics, overseas trade missions, B2B/B2C/B2G linkages, border-market strengthening, exhibitions and digital marketing.',
                'decisions' => 'Assesses whether trade promotion converts into market access for Tanzanian enterprises, with gender-disaggregated SME reach.',
                'parameters' => 'TANTRADE: trade promotion & market linkages. DTD: trade development & market intelligence. TRD: export promotion activities.',
                'datasets' => [
                    'TANTRADE-PRO',
                    'MIT-DTD-PROM',
                    'MIT-EPA'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Value-chain assessments',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'value_chain_assessments',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'Market information datasets',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'market_info_datasets',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'SMEs trained — male-owned',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'smes_trained_male',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'SMEs trained — female-owned',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'smes_trained_female',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'Enterprises coached/mentored',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'enterprises_coached',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'B2B / B2C / B2G linkages',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'b2b_meetings',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'Overseas trade-mission participants',
                                'type' => 'sum',
                                'datasets' => [
                                    'TANTRADE-PRO'
                                ],
                                'field' => 'trade_mission_participants',
                                'basis' => 'Σ TANTRADE-PRO'
                            ],
                            [
                                'label' => 'Exhibitions coordinated',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-DTD-PROM'
                                ],
                                'field' => 'exhibitions_coordinated',
                                'basis' => 'Σ MIT-DTD-PROM'
                            ],
                            [
                                'label' => 'Firms in export-promotion activities',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-EPA'
                                ],
                                'field' => 'participating_firms',
                                'basis' => 'Σ MIT-EPA'
                            ]
                        ]
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'SMEs trained by period',
                        'datasets' => [
                            'TANTRADE-PRO'
                        ],
                        'measure' => [
                            'type' => 'sum',
                            'field' => 'smes_trained_male'
                        ]
                    ]
                ]
            ],
'n10' => [
                'key' => 'N10',
                'tier' => 4,
                'title' => 'Ministry Corporate Performance and Accountability',
                'audience' => 'Ministry management, PS, PO-PSM',
                'frequency' => 'Quarterly',
                'priority' => 'High',
                'summary' => 'Corporate governance of the Ministry itself: MTEF activity performance and budget absorption (DPP), financial management (FAU), M&E and PIPMIS reviews, internal audit engagement and findings resolution, legal matters and contracts follow-up, procurement execution, communication output, staff development and ICT service availability.',
                'decisions' => 'Holds the Ministry accountable for its own delivery: budget absorption, target achievement, audit and legal hygiene, procurement and corporate services that enable the institutions to perform.',
                'parameters' => 'DPP: MTEF activity performance & budget execution. FAU, M&E, IAU, LSU, PMU, GCU, DAHRM, ICTU corporate indicators.',
                'datasets' => [
                    'MIT-PPP-PERF',
                    'MIT-FAU-FIN',
                    'MIT-ME-PERF',
                    'MIT-IAU-AUDIT',
                    'MIT-LSU-LEGAL',
                    'MIT-LSU-CONT',
                    'MIT-PMU-PROC',
                    'MIT-GCU-COMM',
                    'MIT-HR-SDW',
                    'MIT-ICT-SYS'
                ],
                'sections' => [
                    [
                        'type' => 'kpis',
                        'title' => 'Headline indicators',
                        'items' => [
                            [
                                'label' => 'Annual target achievement',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-PPP-PERF'
                                ],
                                'numerator' => 'actual_value',
                                'denominator' => 'annual_target',
                                'format' => 'percent',
                                'basis' => 'actual ÷ annual target'
                            ],
                            [
                                'label' => 'Budget absorption',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-PPP-PERF'
                                ],
                                'numerator' => 'expenditure_tzs',
                                'denominator' => 'planned_budget_tzs',
                                'format' => 'percent',
                                'basis' => 'expenditure ÷ planned budget'
                            ],
                            [
                                'label' => 'Audit findings resolution',
                                'type' => 'ratio',
                                'datasets' => [
                                    'MIT-IAU-AUDIT'
                                ],
                                'numerator' => 'findings_resolved',
                                'denominator' => 'findings_issued',
                                'format' => 'percent',
                                'basis' => 'resolved ÷ issued'
                            ],
                            [
                                'label' => 'Legal matters closed',
                                'type' => 'rate_where',
                                'datasets' => [
                                    'MIT-LSU-LEGAL'
                                ],
                                'field' => 'status',
                                'format' => 'percent',
                                'values' => [
                                    'Closed'
                                ],
                                'match' => 'exact',
                                'basis' => 'closed ÷ all legal matters'
                            ],
                            [
                                'label' => 'Contracts expiring within 90 days',
                                'type' => 'due_within',
                                'datasets' => [
                                    'MIT-LSU-CONT'
                                ],
                                'field' => 'expiry_date',
                                'days' => 90,
                                'basis' => 'MIT-LSU-CONT.expiry_date within 90 days'
                            ],
                            [
                                'label' => 'Tenders completed',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-PMU-PROC'
                                ],
                                'field' => 'tenders_completed',
                                'basis' => 'Σ MIT-PMU-PROC'
                            ],
                            [
                                'label' => 'Financial reports submitted',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-FAU-FIN'
                                ],
                                'field' => 'financial_reports_submitted',
                                'basis' => 'Σ MIT-FAU-FIN'
                            ],
                            [
                                'label' => 'Institutions reviewed (PIPMIS)',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-ME-PERF'
                                ],
                                'field' => 'institutions_reviewed',
                                'basis' => 'Σ MIT-ME-PERF'
                            ],
                            [
                                'label' => 'Press briefings',
                                'type' => 'sum',
                                'datasets' => [
                                    'MIT-GCU-COMM'
                                ],
                                'field' => 'press_briefings',
                                'basis' => 'Σ MIT-GCU-COMM'
                            ],
                            [
                                'label' => 'Network/system uptime',
                                'type' => 'avg',
                                'datasets' => [
                                    'MIT-ICT-SYS'
                                ],
                                'field' => 'network_uptime_percent',
                                'format' => 'percent',
                                'basis' => 'avg MIT-ICT-SYS.uptime'
                            ]
                        ]
                    ],
                    [
                        'type' => 'breakdown',
                        'title' => 'MTEF activities by strategic objective',
                        'dataset' => 'MIT-PPP-PERF',
                        'field' => 'objective_code'
                    ],
                    [
                        'type' => 'trend',
                        'title' => 'Budget absorption by period (%)',
                        'datasets' => [
                            'MIT-PPP-PERF'
                        ],
                        'measure' => [
                            'type' => 'ratio',
                            'numerator' => 'expenditure_tzs',
                            'denominator' => 'planned_budget_tzs',
                            'unit' => '%'
                        ]
                    ]
                ]
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return static::all()[strtolower($key)] ?? null;
    }
}
