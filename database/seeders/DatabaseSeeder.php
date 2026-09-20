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
            DatasetSeeder::class,
            UserSeeder::class,
            SubmissionSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
