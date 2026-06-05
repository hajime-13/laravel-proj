@extends('layouts.app')
@section('title', 'Edit Profile')
@section('breadcrumb', 'Profile / Edit')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-gear me-2 text-primary"></i>Edit Profile</h1>
    <p>Update your personal information and password.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small mb-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <!-- Avatar -->
            <div class="card mb-3">
                <div class="card-header py-3 px-4 fw-semibold"><i class="bi bi-image me-2"></i>Profile Picture</div>
                <div class="card-body px-4 pb-4 d-flex align-items-center gap-4">
                    @if($user->avatar)
                        <img src="{{ \App\Helpers\ImageHelper::url($user->avatar) }}" class="rounded-circle" width="80" height="80" style="object-fit:cover;border:3px solid #e2e8f0" id="avatarPreview">
                    @else
                        <div class="avatar-circle" style="width:80px;height:80px;font-size:1.75rem" id="avatarPlaceholder">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                        <img id="avatarPreview" class="rounded-circle d-none" width="80" height="80" style="object-fit:cover;border:3px solid #e2e8f0">
                    @endif
                    <div>
                        <label class="btn btn-outline-primary btn-sm mb-1">
                            <i class="bi bi-upload me-1"></i> Upload Photo
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                        </label>
                        <p class="text-muted small mb-0">JPG, PNG, GIF up to 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <div class="card mb-3">
                <div class="card-header py-3 px-4 fw-semibold"><i class="bi bi-person me-2"></i>Personal Info</div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Prefer not to say</option>
                                @foreach(['Male','Female','Other'] as $g)
                                    <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" placeholder="Optional">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header py-3 px-4 fw-semibold"><i class="bi bi-key me-2"></i>Change Password <span class="text-muted fw-normal small">(optional)</span></div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium">Current Password</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Required to change password">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('avatarInput')?.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            placeholder?.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
