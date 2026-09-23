@extends('layouts.auth')
@section('title', 'Register')
@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label class="sa-form-label mb-1">Full name</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter full name" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="sa-form-label mb-1">Email address</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="sa-form-label mb-1">Institution</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-bank"></i></span>
            <select name="institution_id" class="form-select" required style="border-left:none;border-radius:0 .6rem .6rem 0">
                <option value="">— Select your institution —</option>
                @foreach($institutions as $inst)<option value="{{ $inst->id }}" @selected(old('institution_id') == $inst->id)>{{ $inst->code }} — {{ $inst->name }}</option>@endforeach
            </select>
        </div>
        <div class="form-text">You will be registered as an Institution Data Officer for the selected institution.</div>
    </div>
    <div class="mb-3">
        <label class="sa-form-label mb-1">Password</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
    </div>
    <div class="mb-4">
        <label class="sa-form-label mb-1">Confirm password</label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
        </div>
    </div>
    <button class="btn btn-sa-primary w-100">Register <i class="bi bi-arrow-right ms-1"></i></button>
    <p class="text-center small mt-3 mb-0">Already have an account? <a class="auth-footer-link" href="{{ route('login') }}">Sign in</a></p>
</form>
@endsection
