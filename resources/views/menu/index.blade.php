@extends('layouts.app')
@section('title', 'Menu Items')
@section('breadcrumb', 'Menu Items')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1><i class="bi bi-menu-button-wide-fill me-2 text-primary"></i>Menu Items</h1>
        <p>Manage the items customers can order from.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
        <i class="bi bi-plus-lg me-1"></i> Add Item
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:70px">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Description</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menuItems as $item)
                    <tr>
                        <td class="ps-4">
                            @if($item->image)
                                <img src="{{ \App\Helpers\ImageHelper::url($item->image) }}"
                                     alt="{{ $item->name }}"
                                     style="width:52px;height:52px;object-fit:cover;border-radius:.5rem;border:1px solid #e2e8f0">
                            @else
                                <div style="width:52px;height:52px;background:#f1f5f9;border-radius:.5rem;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $item->name }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $item->category }}</span></td>
                        <td class="fw-semibold text-success">₱{{ number_format($item->price, 2) }}</td>
                        <td>
                            @if($item->available)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill me-1"></i>No</span>
                            @endif
                        </td>
                        <td class="text-muted small" style="max-width:200px">
                            <span title="{{ $item->description }}">{{ \Illuminate\Support\Str::limit($item->description, 50) }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('menu.edit', $item) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete('{{ route('menu.destroy', $item) }}', 'Delete &quot;{{ $item->name }}&quot;?')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No menu items yet. Add your first item!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($menuItems->hasPages())
    <div class="card-footer bg-transparent">{{ $menuItems->links() }}</div>
    @endif
</div>

<!-- Add Menu Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium">Item Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Chicken Adobo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach(['Appetizer','Main','Side','Dessert','Beverage','Snack'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Price (₱)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-12">
                            <label class="form-label small fw-medium">Item Photo (optional)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="addImagePreviewWrap" style="display:none">
                                    <img id="addImagePreview"
                                         style="width:72px;height:72px;object-fit:cover;border-radius:.5rem;border:2px solid #4f46e5">
                                </div>
                                <div id="addImagePlaceholder"
                                     style="width:72px;height:72px;background:#f1f5f9;border-radius:.5rem;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                                <div>
                                    <label class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-upload me-1"></i> Choose Photo
                                        <input type="file" name="image" id="addImageInput" class="d-none" accept="image/*">
                                    </label>
                                    <p class="text-muted small mb-0 mt-1">JPG, PNG, GIF, WebP — max 2MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="available" class="form-check-input" id="availableCheck" checked>
                                <label class="form-check-label small" for="availableCheck">Available for ordering</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
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

    // Image preview for Add modal
    document.getElementById('addImageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('addImagePreview').src = e.target.result;
            document.getElementById('addImagePreviewWrap').style.display = '';
            document.getElementById('addImagePlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    @if($errors->any())
        new bootstrap.Modal(document.getElementById('addMenuModal')).show();
    @endif
</script>
@endpush
