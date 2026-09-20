<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Administrator', 'email' => 'admin@viwanda.go.tz',
                'role' => 'System Administrator', 'institution' => null,
            ],
            [
                'name' => 'Ministry Data Manager', 'email' => 'manager@viwanda.go.tz',
                'role' => 'Ministry Data Manager', 'institution' => null,
            ],
            [
                'name' => 'Department Data Reviewer', 'email' => 'reviewer@viwanda.go.tz',
                'role' => 'Department Data Reviewer', 'institution' => null,
            ],
            [
                'name' => 'FCT Data Officer', 'email' => 'fct.officer@fct.go.tz',
                'role' => 'Institution Data Officer', 'institution' => 'FCT',
            ],
            [
                'name' => 'FCT Institution Admin', 'email' => 'admin.fct@fct.go.tz',
                'role' => 'Institution Admin', 'institution' => 'FCT',
            ],
            [
                'name' => 'FCT Supervisor', 'email' => 'fct.supervisor@fct.go.tz',
                'role' => 'Institution Supervisor', 'institution' => 'FCT',
            ],
            [
                'name' => 'FCT Accounting Officer', 'email' => 'fct.ao@fct.go.tz',
                'role' => 'Institution Accounting Officer', 'institution' => 'FCT',
            ],
            [
                'name' => 'FCC Integration Account', 'email' => 'api.fcc@fcc.go.tz',
                'role' => 'Institution API Account', 'institution' => 'FCC',
            ],
            [
                'name' => 'Executive Viewer', 'email' => 'executive@viwanda.go.tz',
                'role' => 'Executive / Report Consumer', 'institution' => null,
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'institution_id' => $data['institution']
                        ? Institution::where('code', $data['institution'])->firstOrFail()->id
                        : null,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
