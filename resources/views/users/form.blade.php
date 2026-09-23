@extends('layouts.app')

@section('title', $user->exists ? 'Edit User: ' . $user->name : 'New User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->exists ? 'Edit ' . $user->name : 'New user' }}</li>
@endsection

@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="bi bi-person me-2"></i>Account Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password {{ $user->exists ? '(leave blank to keep current)' : '' }} {!! $user->exists ? '' : '<span class="text-danger">*</span>' !!}</label>
                            <input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Institution</label>
                            @if($lockedInstitution)
                                <input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">
                                <input type="text" class="form-control" value="{{ $institutions->first()->name ?? auth()->user()->institution->name }}" disabled>
                                <div class="form-text">Institution administrators can only manage users of their own institution.</div>
                            @else
                                <select name="institution_id" class="form-select">
                                    <option value="">— Ministry (no institution) —</option>
                                    @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected((string) old('institution_id', $user->institution_id) === (string) $institution->id)>{{ $institution->code }} — {{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @if(! $lockedInstitution)
                        <div class="col-md-6">
                            <label class="form-label">Ministry Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected((string) old('department_id', $user->department_id) === (string) $dept->id)>{{ $dept->code }} — {{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Required for <strong>Ministry Department Data Officer</strong> accounts — limits them to their department's datasets.</div>
                        </div>
                        @endif
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $user->exists ? $user->is_active : true))>
                                <label class="form-check-label" for="isActive">Active account</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-shield-lock me-2"></i>Roles <span class="text-danger">*</span></div>
                <div class="card-body" style="max-height: 480px; overflow-y: auto;">
                    @php $selectedRoles = old('roles', $user->roles->pluck('name')->all()); @endphp
                    @foreach($roles as $role)
                    <div class="border rounded p-2 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" @checked(in_array($role->name, $selectedRoles))>
                            <label class="form-check-label fw-semibold" for="role_{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                        <div class="small text-muted mt-1">
                            @foreach($role->permissions->pluck('name') as $perm)
                                <span class="badge text-bg-light border me-1 mb-1">{{ $perm }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @if($lockedInstitution)
                    <div class="form-text">Only institutional roles can be assigned.</div>
                    @endif
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $user->exists ? 'Update User' : 'Create User' }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
