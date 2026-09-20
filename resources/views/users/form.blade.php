@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'New User')
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-lg-7"><div class="card"><div class="card-header">Account</div><div class="card-body row g-3">
            <div class="col-md-6"><label class="form-label">Full name *</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label">Password {{ $user->exists ? '(leave blank to keep)' : '*' }}</label><input type="password" name="password" class="form-control" autocomplete="new-password"></div>
            <div class="col-md-6"><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Institution</label>
                <select name="institution_id" class="form-select">
                    <option value="">— Ministry / none —</option>
                    @foreach($institutions as $inst)<option value="{{ $inst->id }}" @selected(old('institution_id', $user->institution_id) == $inst->id)>{{ $inst->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end"><div class="form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $user->is_active ?? true))>
                <label class="form-check-label" for="active">Account active</label>
            </div></div>
        </div></div></div>
        <div class="col-lg-5"><div class="card"><div class="card-header">Roles *</div><div class="card-body">
            @foreach($roles as $role)
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                    @checked(in_array($role->name, old('roles', $user->exists ? $user->roles->pluck('name')->all() : [])))>
                <label class="form-check-label" for="role_{{ $role->id }}">
                    <strong>{{ $role->name }}</strong><br>
                    <span class="text-muted small">{{ $role->permissions->pluck('name')->join(', ') }}</span>
                </label>
            </div>
            @endforeach
        </div></div>
        <div class="text-end mt-3">
            @if($user->exists)
            <button form="deleteUser" class="btn btn-outline-danger" onclick="return confirm('Delete this account?')"><i class="bi bi-trash"></i> Delete</button>
            @endif
            <button class="btn btn-success"><i class="bi bi-save"></i> Save user</button>
        </div>
        </div>
    </div>
</form>
@if($user->exists)
<form id="deleteUser" method="POST" action="{{ route('users.destroy', $user) }}" class="d-none">@csrf @method('DELETE')</form>
@endif
@endsection
