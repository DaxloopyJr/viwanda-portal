<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /** Roles an Institution Admin may assign to users of their own institution. */
    public const INSTITUTION_ROLES = [
        'Institution Admin', 'Institution Data Officer', 'Institution Supervisor',
        'Institution Accounting Officer', 'Institution API Account',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'submissions.view', 'submissions.create', 'submissions.edit-own', 'submissions.delete-own',
            'submissions.submit-internal', 'submissions.review-internal', 'submissions.approve-internal',
            'submissions.review', 'submissions.accept', 'submissions.return', 'submissions.reject', 'submissions.publish',
            'reports.view', 'reports.export',
            'institutions.manage',
            'datasets.manage', 'datasets.manage-own',
            'users.manage', 'users.manage-own', 'roles.manage',
            'settings.manage',
            'audit.view',
            'api.access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'System Administrator' => $permissions,
            'Ministry Data Manager' => [
                'dashboard.view', 'submissions.view', 'submissions.review',
                'submissions.accept', 'submissions.return', 'submissions.reject', 'submissions.publish',
                'reports.view', 'reports.export', 'audit.view', 'datasets.manage',
            ],
            'Department Data Reviewer' => [
                'dashboard.view', 'submissions.view', 'submissions.review',
                'submissions.accept', 'submissions.return', 'submissions.reject', 'reports.view',
            ],
            // Institutional roles — scoped to the user's own institution
            'Institution Admin' => [
                'dashboard.view', 'submissions.view', 'reports.view',
                'users.manage-own', 'datasets.manage-own',
            ],
            'Institution Data Officer' => [
                'dashboard.view', 'submissions.view', 'submissions.create',
                'submissions.edit-own', 'submissions.delete-own', 'submissions.submit-internal',
            ],
            'Institution Supervisor' => [
                'dashboard.view', 'submissions.view', 'submissions.review-internal',
            ],
            'Institution Accounting Officer' => [
                'dashboard.view', 'submissions.view', 'submissions.approve-internal',
            ],
            'Institution API Account' => ['api.access'],
            'Executive / Report Consumer' => ['dashboard.view', 'reports.view'],
        ];

        foreach ($roles as $name => $grants) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->syncPermissions($grants);
        }
    }
}
