<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            InstitutionSeeder::class,
            DepartmentSeeder::class,
            DatasetSeeder::class,
            ConsumerSeeder::class,
            SubmissionPeriodSeeder::class,
            UserSeeder::class,
            SubmissionSeeder::class,
            DemoReportDataSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
