@extends('layouts.app')

@section('title', 'Profile & API Tokens')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profile & API Tokens</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person-circle me-2"></i>My Account</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $user->name }}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4">Institution</dt><dd class="col-sm-8">{{ $user->institution->name ?? '— Ministry —' }}</dd>
                    <dt class="col-sm-4">Roles</dt>
                    <dd class="col-sm-8">
                        @foreach($user->roles as $role)
                            <span class="badge text-bg-primary me-1">{{ $role->name }}</span>
                        @endforeach
                    </dd>
                    <dt class="col-sm-4">Member since</dt><dd class="col-sm-8">{{ $user->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-key me-2"></i>Change Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Password</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        @can('api.access')
        <div class="card">
            <div class="card-header"><i class="bi bi-plug me-2"></i>API Tokens</div>
            <div class="card-body">
                <p class="small text-muted">API tokens allow system-to-system submission of data (ability: <code>submit</code>). Keep tokens secret — they are shown only once when created.</p>

                @if(session('api_token'))
                <div class="alert alert-success">
                    <div class="fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>New token created — copy it now:</div>
                    <code class="user-select-all">{{ session('api_token') }}</code>
                </div>
                @endif

                <form method="POST" action="{{ route('profile.tokens.create') }}" class="d-flex gap-2 mb-3">
                    @csrf
                    <input type="text" name="token_name" class="form-control" placeholder="Token name, e.g. MIS integration" required maxlength="60">
                    <button class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg me-1"></i>Create Token</button>
                </form>

                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Name</th><th>Created</th><th>Last used</th><th></th></tr></thead>
                    <tbody>
                        @forelse($user->tokens as $token)
                        <tr>
                            <td>{{ $token->name }}</td>
                            <td class="text-muted small">{{ $token->created_at->format('d M Y H:i') }}</td>
                            <td class="text-muted small">{{ $token->last_used_at?->format('d M Y H:i') ?? 'Never' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('profile.tokens.revoke', $token->id) }}" onsubmit="return confirm('Revoke this token?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No API tokens.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-muted small">
                <i class="bi bi-info-circle me-1"></i>Your role does not include API access. Contact your administrator if your institution needs system-to-system integration.
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
