@extends('layouts.app')
@section('title', 'Users')
@section('breadcrumb', 'Users Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1><i class="bi bi-people-fill me-2 text-primary"></i>Users Management</h1>
        <p>Manage all registered users.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add User
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:40px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" class="rounded-circle" width="34" height="34" style="object-fit:cover">
                                @else
                                    <div class="avatar-circle">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    @if($user->id === Auth::id())
                                        <span class="badge bg-primary" style="font-size:.65rem">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            @if($user->id !== Auth::id())
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete('{{ route('users.destroy', $user) }}', 'Delete user &quot;{{ $user->name }}&quot;?')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-transparent">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete form -->
<form id="deleteForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete(action, msg) {
        if (confirm(msg)) {
            const form = document.getElementById('deleteForm');
            form.action = action;
            form.submit();
        }
    }
    // Auto-open modal on validation error
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('addUserModal')).show();
    @endif
</script>
@endpush
