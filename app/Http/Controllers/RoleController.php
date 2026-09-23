<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /** Role that must never be renamed or deleted (lockout protection). */
    private const PROTECTED_ROLE = 'System Administrator';

    public function index()
    {
        $this->authorize('roles.manage');

        return view('roles.index');
    }

    /** JSON feed for the AJAX roles table (DataTables). */
    public function datatable()
    {
        $this->authorize('roles.manage');

        $rows = Role::with('permissions')->withCount('users')->orderBy('name')->get()
            ->map(function (Role $role) {
                $perms = $role->permissions->map(fn ($p) => '<span class="badge text-bg-light border me-1 mb-1">'.e($p->name).'</span>')->implode('');
                $delete = '';
                if ($role->name !== self::PROTECTED_ROLE) {
                    $delete = '<form method="POST" action="'.route('roles.destroy', $role).'" class="d-inline" onsubmit="return confirm(\'Delete role '.e($role->name).'?\')">'
                        .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                        .'<input type="hidden" name="_method" value="DELETE">'
                        .'<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
                }

                return [
                    'name' => '<span class="fw-semibold">'.e($role->name).'</span>'
                        .($role->name === self::PROTECTED_ROLE ? ' <span class="badge text-bg-warning">Protected</span>' : ''),
                    'permissions' => $perms,
                    'permissions_count' => (int) $role->permissions->count(),
                    'users_count' => (int) $role->users_count,
                    'actions' => '<a href="'.route('roles.edit', $role).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> '.$delete,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function create()
    {
        $this->authorize('roles.manage');

        return view('roles.form', [
            'role' => new Role,
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('roles.manage');
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::record('role.created', $role, null, ['name' => $role->name, 'permissions' => $data['permissions'] ?? []]);

        return redirect()->route('roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $this->authorize('roles.manage');

        return view('roles.form', [
            'role' => $role->load('permissions'),
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('roles.manage');
        $data = $this->validated($request, $role->id);

        if ($role->name === self::PROTECTED_ROLE) {
            // Never rename or strip the administrative core from the protected role.
            $data['name'] = self::PROTECTED_ROLE;
            $data['permissions'] = array_values(array_unique(array_merge(
                $data['permissions'] ?? [],
                ['users.manage', 'roles.manage', 'settings.manage']
            )));
        }

        $old = ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->all()];
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::record('role.updated', $role, $old, ['name' => $role->name, 'permissions' => $data['permissions'] ?? []]);

        return redirect()->route('roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('roles.manage');
        abort_if($role->name === self::PROTECTED_ROLE, 422, 'The System Administrator role cannot be deleted.');
        abort_if($role->users()->exists(), 422, 'Cannot delete a role that is still assigned to users. Reassign those users first.');

        AuditLog::record('role.deleted', $role, ['name' => $role->name]);
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Role deleted.');
    }

    /** Permissions grouped by their module prefix (the part before the dot). */
    private function permissionGroups()
    {
        return Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0])
            ->sortKeys();
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'.($id ? ','.$id : '')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);
    }
}
