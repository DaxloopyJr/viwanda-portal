@extends('layouts.auth')
@section('title', __('Sign in'))
@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="sa-form-label mb-1">{{ __('Email address') }}</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="{{ __('Enter email address') }}" required autofocus>
        </div>
    </div>
    <div class="mb-3">
        <label class="sa-form-label mb-1">{{ __('Password') }}</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="{{ __('Enter password') }}" required>
        </div>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label small" for="remember">{{ __('Remember me') }}</label>
    </div>
    <button class="btn btn-sa-primary w-100">{{ __('Sign in') }} <i class="bi bi-arrow-right ms-1"></i></button>
    <p class="text-center small mt-3 mb-0 text-muted">{{ __('Accounts are issued by your institution administrator.') }}</p>
    <p class="text-center small mt-2 mb-0"><a class="auth-footer-link" href="{{ route('landing') }}">&larr; {{ __('Back to portal home') }}</a></p>
</form>
@endsection
