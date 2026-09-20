@extends('layouts.app')
@section('title', 'Users & Roles')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Portal accounts and role assignments (RBAC via spatie/laravel-permission).</p>
    <a href="{{ route('users.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> New User</a>
</div>
<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped table-hover mb-0">
        <thead><tr><th>Name</th><th>Email</th><th>Institution</th><th>Roles</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td class="fw-semibold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->institution?->code ?? '—' }}</td>
                <td>@foreach($u->roles as $role)<span class="badge text-bg-primary me-1">{{ $role->name }}</span>@endforeach</td>
                <td><span class="badge text-bg-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Active' : 'Disabled' }}</span></td>
                <td><a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($users->hasPages())<div class="card-footer">{{ $users->links() }}</div>@endif
</div>
@endsection
