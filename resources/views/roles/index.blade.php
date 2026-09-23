@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Roles & Permissions</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-lock me-2"></i>Roles and their permissions</span>
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Role</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th data-priority="2"># Perms</th>
                        <th data-priority="2">Users</th>
                        <th class="no-export" data-priority="1"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
vpDataTable('#rolesTable', '{{ route('roles.datatable') }}', [
    { data: 'name', name: 'name' },
    { data: 'permissions', name: 'permissions', orderable: false },
    { data: 'permissions_count', name: 'permissions_count' },
    { data: 'users_count', name: 'users_count' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
]);
</script>
@endpush
