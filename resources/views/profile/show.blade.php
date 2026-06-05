@extends('layouts.app')
@section('title', 'My Profile')
@section('breadcrumb', 'Profile')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h1>
    <p>View and manage your account information.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 mb-4 pb-4" style="border-bottom:1px solid #f1f5f9">
                    @if($user->avatar)
                        <img src="{{ \App\Helpers\ImageHelper::url($user->avatar) }}" class="rounded-circle" width="90" height="90" style="object-fit:cover;border:3px solid #e2e8f0">
                    @else
                        <div class="avatar-circle avatar-lg">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    @endif
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted mb-1 small">{{ $user->email }}</p>
                        <span class="badge bg-primary-subtle text-primary">{{ $user->is_admin ? 'Admin' : 'User' }}</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Full Name</label>
                        <span class="fw-medium">{{ $user->name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Email</label>
                        <span class="fw-medium">{{ $user->email }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Gender</label>
                        <span class="fw-medium">{{ $user->gender ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Address</label>
                        <span class="fw-medium">{{ $user->address ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Member Since</label>
                        <span class="fw-medium">{{ $user->created_at->format('F d, Y') }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil-fill me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
