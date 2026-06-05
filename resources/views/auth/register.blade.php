@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<div class="auth-brand">
    <div class="brand-icon">
        <i class="bi bi-clipboard2-check-fill text-white fs-3"></i>
    </div>
    <h1>Create Account</h1>
    <p>Join OrderList to manage your menu & orders</p>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-medium small">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   placeholder="Your full name" value="{{ old('name') }}" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-medium small">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com" value="{{ old('email') }}" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-medium small">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Min. 8 characters" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-medium small">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Repeat password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="bi bi-person-plus me-1"></i> Create Account
    </button>
</form>

<p class="text-center small mt-3 mb-0 text-muted">
    Already have an account?
    <a href="{{ route('login') }}" class="text-decoration-none fw-medium" style="color:#4f46e5">Sign in</a>
</p>
@endsection
