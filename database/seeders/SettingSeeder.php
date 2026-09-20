<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('site_name', 'Viwanda na Biashara Portal');
        Setting::set('site_tagline', 'Ministry of Industry and Trade — Data Collection and Reporting');
    }
}
