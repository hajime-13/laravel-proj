@extends('layouts.app')
@section('title', 'Edit User')
@section('breadcrumb', 'Users / Edit')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-gear me-2 text-primary"></i>Edit User</h1>
    <p>Update user information.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-medium">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
