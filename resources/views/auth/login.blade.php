@extends('layouts.auth')
@section('title', 'Sign in')
@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button class="btn btn-primary w-100" style="background:#1a3c6e">Sign in</button>
    <p class="text-center small mt-3 mb-0">Institution officer? <a href="{{ route('register') }}">Register an account</a></p>
</form>
@endsection
