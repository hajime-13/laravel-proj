@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="auth-brand">
    <div class="brand-icon">
        <i class="bi bi-clipboard2-check-fill text-white fs-3"></i>
    </div>
    <h1>Welcome back</h1>
    <p>Sign in to your OrderList account</p>
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

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-medium small">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-medium small">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control"
                   placeholder="Your password" required>
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small" for="remember">Remember me</label>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
    </button>
</form>

<p class="text-center small mt-3 mb-0 text-muted">
    Don't have an account?
    <a href="{{ route('register') }}" class="text-decoration-none fw-medium" style="color:#4f46e5">Register</a>
</p>
@endsection
