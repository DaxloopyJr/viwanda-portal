@extends('layouts.app')

@section('title', $ownOnly ? 'My Institution Users' : 'Users')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>{{ $ownOnly ? 'User accounts of your institution' : 'All user accounts' }}</span>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New User</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Name</th><th>Email</th><th>Institution</th><th>Roles</th><th>Status</th>
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
vpDataTable('#usersTable', '{{ route('users.datatable') }}', [
    { data: 'name', name: 'name' },
    { data: 'email', name: 'email' },
    { data: 'institution', name: 'institution' },
    { data: 'roles', name: 'roles', orderable: false },
    { data: 'status', name: 'status' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
]);
</script>
@endpush
