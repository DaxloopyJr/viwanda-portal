@extends('layouts.auth')
@section('title', 'Register')
@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Full name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Institutional email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Institution</label>
        <select name="institution_id" class="form-select" required>
            <option value="">Select institution…</option>
            @foreach($institutions as $institution)
                <option value="{{ $institution->id }}" @selected(old('institution_id') == $institution->id)>{{ $institution->name }} ({{ $institution->code }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100" style="background:#1a3c6e">Register</button>
    <p class="text-center small mt-3 mb-0">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
</form>
@endsection
