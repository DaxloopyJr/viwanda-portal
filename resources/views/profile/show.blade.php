@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="row g-3">
    <div class="col-lg-5"><div class="card"><div class="card-header">Account</div><div class="card-body">
        <dl class="row small mb-0">
            <dt class="col-4">Name</dt><dd class="col-8">{{ $user->name }}</dd>
            <dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
            <dt class="col-4">Roles</dt><dd class="col-8">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</dd>
            <dt class="col-4">Institution</dt><dd class="col-8">{{ $user->institution?->name ?? 'Ministry' }}</dd>
        </dl>
    </div></div>

    <div class="card mt-3"><div class="card-header">Change password</div><div class="card-body">
        <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
            <div class="mb-2"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">New password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Confirm new password</label><input type="password" name="password_confirmation" class="form-control" required></div>
            <button class="btn btn-primary btn-sm">Update password</button>
        </form>
    </div></div></div>

    @can('api.access')
    <div class="col-lg-7"><div class="card"><div class="card-header">API Integration Tokens</div><div class="card-body">
        <p class="text-muted small">Tokens authenticate your institution's system to the Portal API (<code>Authorization: Bearer &lt;token&gt;</code>). Store them securely; they are shown only once.</p>
        @if(session('api_token'))
            <div class="alert alert-success"><strong>New token (copy now):</strong><br><code class="user-select-all">{{ session('api_token') }}</code></div>
        @endif
        <form method="POST" action="{{ route('profile.tokens.create') }}" class="d-flex gap-2 mb-3">@csrf
            <input name="token_name" class="form-control" placeholder="Token name, e.g. Production FCC system" required>
            <button class="btn btn-success text-nowrap"><i class="bi bi-plus-lg"></i> Create token</button>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Created</th><th>Last used</th><th></th></tr></thead>
            <tbody>
            @foreach($user->tokens as $token)
                <tr>
                    <td>{{ $token->name }}</td>
                    <td>{{ $token->created_at->format('d M Y') }}</td>
                    <td>{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                    <td><form method="POST" action="{{ route('profile.tokens.revoke', $token->id) }}" onsubmit="return confirm('Revoke this token?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Revoke</button></form></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div></div>
    @endcan
</div>
@endsection
