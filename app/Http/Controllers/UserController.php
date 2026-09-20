<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeManage();

        return view('users.index', [
            'users' => $this->scopedQuery()->with(['institution', 'roles'])->orderBy('name')->paginate(20),
            'ownOnly' => $this->isInstitutionAdmin(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('users.form', [
            'user' => new User,
            'roles' => $this->assignableRoles(),
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isInstitutionAdmin(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);
        $data = $this->enforceInstitutionScope($data);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'institution_id' => $data['institution_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);
        $user->syncRoles($data['roles'] ?? []);
        AuditLog::record('user.created', $user, null, ['email' => $user->email, 'roles' => $data['roles'] ?? []]);

        return redirect()->route('users.index')->with('success', 'User account created.');
    }

    public function edit(User $user)
    {
        $this->authorizeManage();
        $this->authorizeTarget($user);

        return view('users.form', [
            'user' => $user->load('roles'),
            'roles' => $this->assignableRoles(),
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isInstitutionAdmin(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeManage();
        $this->authorizeTarget($user);
        $data = $this->validated($request, $user->id, false);
        $data = $this->enforceInstitutionScope($data);

        $old = ['email' => $user->email, 'roles' => $user->roles->pluck('name')->all(), 'is_active' => $user->is_active];
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'institution_id' => $data['institution_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ] + (empty($data['password']) ? [] : ['password' => Hash::make($data['password'])]));
        $user->syncRoles($data['roles'] ?? []);
        AuditLog::record('user.updated', $user, $old, ['email' => $user->email, 'roles' => $data['roles'] ?? []]);

        return redirect()->route('users.index')->with('success', 'User account updated.');
    }

    public function destroy(User $user)
    {
        $this->authorizeManage();
        $this->authorizeTarget($user);
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');
        AuditLog::record('user.deleted', $user, ['email' => $user->email]);
        $user->delete();

        return back()->with('success', 'User account deleted.');
    }

    /** System administrators manage all users; institution admins only their own institution. */
    private function authorizeManage(): void
    {
        abort_unless(
            auth()->user()->can('users.manage') || auth()->user()->can('users.manage-own'),
            403
        );
    }

    private function isInstitutionAdmin(): bool
    {
        $user = auth()->user();

        return $user->can('users.manage-own') && ! $user->can('users.manage');
    }

    private function scopedQuery()
    {
        $query = User::query();
        if ($this->isInstitutionAdmin()) {
            $query->where('institution_id', auth()->user()->institution_id);
        }

        return $query;
    }

    private function authorizeTarget(User $user): void
    {
        if ($this->isInstitutionAdmin()) {
            abort_unless(
                $user->institution_id && $user->institution_id === auth()->user()->institution_id,
                403, 'You can only manage users of your own institution.'
            );
        }
    }

    private function assignableRoles()
    {
        $query = Role::orderBy('name');
        if ($this->isInstitutionAdmin()) {
            $query->whereIn('name', RolePermissionSeeder::INSTITUTION_ROLES);
        }

        return $query->get();
    }

    private function assignableInstitutions()
    {
        if ($this->isInstitutionAdmin()) {
            return Institution::where('id', auth()->user()->institution_id)->get();
        }

        return Institution::orderBy('name')->get();
    }

    /** Institution admins cannot move users outside their institution or assign ministry roles. */
    private function enforceInstitutionScope(array $data): array
    {
        if ($this->isInstitutionAdmin()) {
            $data['institution_id'] = auth()->user()->institution_id;
            $allowed = Role::whereIn('name', RolePermissionSeeder::INSTITUTION_ROLES)->pluck('name')->all();
            $data['roles'] = array_values(array_intersect($data['roles'] ?? [], $allowed));
            abort_if(empty($data['roles']), 422, 'Select at least one institutional role.');
        }

        return $data;
    }

    private function validated(Request $request, ?int $id = null, bool $passwordRequired = true): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($id ? ','.$id : '')],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'confirmed', Password::min(8)],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'],
            'is_active' => ['boolean'],
        ]);
    }
}
