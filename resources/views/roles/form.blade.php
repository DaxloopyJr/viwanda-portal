@extends('layouts.app')

@section('title', $role->exists ? 'Edit Role: ' . $role->name : 'New Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active">{{ $role->exists ? 'Edit ' . $role->name : 'New role' }}</li>
@endsection

@section('content')
<form method="POST" action="{{ $role->exists ? route('roles.update', $role) : route('roles.store') }}">
    @csrf
    @if($role->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-shield-lock me-2"></i>Role Details</div>
                <div class="card-body">
                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required maxlength="100"
                           {{ $role->name === 'System Administrator' ? 'readonly' : '' }}>
                    @if($role->name === 'System Administrator')
                        <div class="form-text">The protected administrative role — it cannot be renamed, and its core management permissions are always kept.</div>
                    @else
                        <div class="form-text">A descriptive name, e.g. <em>Regional Data Reviewer</em>.</div>
                    @endif
                    <div class="mt-3 small text-muted">
                        Select the permissions this role grants. Users with multiple roles receive the union of all their roles' permissions.
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $role->exists ? 'Update Role' : 'Create Role' }}</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key me-2"></i>Permissions</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="vpSetAllPerms(true)">Select all</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="vpSetAllPerms(false)">Clear</button>
                    </div>
                </div>
                <div class="card-body">
                    @php $selected = old('permissions', $role->exists ? $role->permissions->pluck('name')->all() : []); @endphp
                    <div class="row g-3">
                        @foreach($permissionGroups as $group => $permissions)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-capitalize"><i class="bi bi-folder2-open me-1"></i>{{ $group }}</span>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input group-toggle" type="checkbox" data-group="{{ $group }}" id="grp_{{ $group }}">
                                        <label class="form-check-label small text-muted" for="grp_{{ $group }}">all</label>
                                    </div>
                                </div>
                                @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input class="form-check-input perm-check perm-{{ $group }}" type="checkbox"
                                           name="permissions[]" value="{{ $permission->name }}"
                                           id="perm_{{ $permission->id }}" @checked(in_array($permission->name, $selected))>
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function vpSetAllPerms(state) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = state);
}
document.querySelectorAll('.group-toggle').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
        document.querySelectorAll('.perm-' + this.dataset.group).forEach(cb => cb.checked = this.checked);
    });
});
</script>
@endpush
