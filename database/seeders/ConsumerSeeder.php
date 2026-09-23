<?php

namespace Database\Seeders;

use App\Models\Consumer;
use Illuminate\Database\Seeder;

class ConsumerSeeder extends Seeder
{
    public function run(): void
    {
        // Global data consumers (ministry-configured), as referenced in the
        // FCT and TBS data-requirements documents.
        $consumers = [
            ['code' => 'MIT', 'name' => 'Ministry of Industry and Trade'],
            ['code' => 'TR', 'name' => 'Tribunals and Regulatory Bodies'],
            ['code' => 'TRA', 'name' => 'Tanzania Revenue Authority'],
            ['code' => 'EWURA', 'name' => 'Energy and Water Utilities Regulatory Authority'],
            ['code' => 'NBS', 'name' => 'National Bureau of Statistics'],
            ['code' => 'WTO', 'name' => 'World Trade Organization (TBT/SPS enquiry points)'],
            ['code' => 'Regulators', 'name' => 'Sector Regulatory Authorities'],
            ['code' => 'Industry', 'name' => 'Industry and Manufacturers'],
            ['code' => 'SMEs', 'name' => 'Small and Medium Enterprises'],
            ['code' => 'Importers', 'name' => 'Importers'],
            ['code' => 'Exporters', 'name' => 'Exporters'],
            ['code' => 'Public', 'name' => 'General Public'],
        ];

        foreach ($consumers as $consumer) {
            Consumer::firstOrCreate(
                ['code' => $consumer['code'], 'institution_id' => null],
                $consumer + ['institution_id' => null]
            );
        }
    }
}
