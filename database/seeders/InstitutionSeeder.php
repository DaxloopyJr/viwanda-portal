<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            ['code' => 'BRELA', 'name' => 'Business Registrations and Licensing Agency', 'integration_mode' => 'api'],
            ['code' => 'WHLB', 'name' => 'Warehouse Licensing Board', 'integration_mode' => 'manual'],
            ['code' => 'TBS', 'name' => 'Tanzania Bureau of Standards', 'integration_mode' => 'api'],
            ['code' => 'SIDO', 'name' => 'Small Industries Development Organization', 'integration_mode' => 'manual'],
            ['code' => 'TIRDO', 'name' => 'Tanzania Industrial Research and Development Organization', 'integration_mode' => 'manual'],
            ['code' => 'NDC', 'name' => 'National Development Corporation', 'integration_mode' => 'manual'],
            ['code' => 'WMA', 'name' => 'Weights and Measures Agency', 'integration_mode' => 'manual'],
            ['code' => 'FCC', 'name' => 'Fair Competition Commission', 'integration_mode' => 'api'],
            ['code' => 'FCT', 'name' => 'Fair Competition Tribunal', 'integration_mode' => 'manual'],
            ['code' => 'TEMDO', 'name' => 'Tanzania Engineering and Manufacturing Design Organization', 'integration_mode' => 'manual'],
            ['code' => 'CARMATEC', 'name' => 'Centre for Agricultural Mechanization and Rural Technology', 'integration_mode' => 'manual'],
            ['code' => 'CBE', 'name' => 'College of Business Education', 'integration_mode' => 'manual'],
            ['code' => 'TADB', 'name' => 'Tanzania Agricultural Development Bank', 'integration_mode' => 'api'],
            ['code' => 'TANTRADE', 'name' => 'Tanzania Trade Development Authority', 'integration_mode' => 'manual'],
        ];

        foreach ($institutions as $institution) {
            Institution::firstOrCreate(
                ['code' => $institution['code']],
                $institution + ['contact_email' => strtolower($institution['code']).'@example.go.tz']
            );
        }
    }
}
