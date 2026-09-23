<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ministry departments and units per the MIT data-requirements document
        // ("Ministry of Industry and Trade with Institutions — Data Requirements
        // for Developing Data Warehouse").
        $departments = [
            ['code' => 'IND', 'name' => 'Department of Industrial Development', 'description' => 'Industrial strategies, diagnostic studies, industrial surveys, KAIZEN, heavy/light industry development and industrial performance monitoring.'],
            ['code' => 'TRD', 'name' => 'Trade Department', 'description' => 'Domestic and international trade policy and facilitation.'],
            ['code' => 'PPP', 'name' => 'Department of Policy and Planning', 'description' => 'Sector policy, MTEF budget framework, strategic plan, action plans and performance reporting.'],
            ['code' => 'DAHRM', 'name' => 'Department of Administration and Human Resource Management', 'description' => 'Staff development and welfare, HIV/AIDS & NCD interventions, anti-corruption programmes, personnel emoluments and data cleaning.'],
            ['code' => 'DTD', 'name' => 'Department of Trade and Development', 'description' => 'Trade promotion, trade formalization, public-private dialogue (MPPD), DITF and Nane Nane exhibitions, market intelligence.'],
            ['code' => 'DTI', 'name' => 'Department of Trade Integration', 'description' => 'Market access negotiations (EAC, AfCFTA, TFTA, SADC, AGOA), NTB elimination, TBT/SPS/TFA committees and trade remedies.'],
            ['code' => 'SME', 'name' => 'Department of Small and Medium Enterprises', 'description' => 'SME policy, loan-scheme awareness, business formalization, SME industrial clusters and market access for MSMEs.'],
            ['code' => 'FAU', 'name' => 'Finance and Accounting Unit', 'description' => 'Payroll, audit queries, financial statements and reports, payments and disbursements to MIT institutions.'],
            ['code' => 'ICTU', 'name' => 'Information and Communication Technology Unit', 'description' => 'LAN/internet/systems, industrial databases and the MIT data warehouse, ICT governance and capacity building.'],
            ['code' => 'PMU', 'name' => 'Procurement Management Unit', 'description' => 'Annual procurement plan, tender board secretariat, stocktaking, asset verification and disposal.'],
            ['code' => 'LSU', 'name' => 'Legal Services Unit', 'description' => 'Trade-related contracts and litigation, legislative instruments, legal advisory, contracts/MoUs/guarantees register.'],
            ['code' => 'ME', 'name' => 'Monitoring and Evaluation Unit', 'description' => 'M&E of policies, strategic plans, budgets and projects; NKRA reporting; institutional performance reviews (PIPMIS).'],
            ['code' => 'IAU', 'name' => 'Internal Audit Unit', 'description' => 'Risk-based internal audit plans, audit engagements, audit committees and follow-up of audit findings.'],
            ['code' => 'GCU', 'name' => 'Government Communication Unit', 'description' => 'Ministerial communication strategy, press briefings, media engagement, publications and official online channels.'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['code' => $department['code']], $department);
        }
    }
}
