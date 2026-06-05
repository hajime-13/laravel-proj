@extends('layouts.app')
@section('title', 'Edit Menu Item')
@section('breadcrumb', 'Menu / Edit')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Menu Item</h1>
    <p>Update the details of this menu item.</p>
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
                <form method="POST" action="{{ route('menu.update', $menu) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Item Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Category</label>
                            <select name="category" class="form-select" required>
                                @foreach(['Appetizer','Main','Side','Dessert','Beverage','Snack'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $menu->category)===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Price (₱)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $menu->price) }}" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description) }}</textarea>
                    </div>

                    <!-- Image Section -->
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Item Photo</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if($menu->image)
                                <img id="editImagePreview"
                                     src="{{ \App\Helpers\ImageHelper::url($menu->image) }}"
                                     alt="{{ $menu->name }}"
                                     style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid #4f46e5">
                            @else
                                <div id="editImagePlaceholder"
                                     style="width:80px;height:80px;background:#f1f5f9;border-radius:.5rem;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-image text-muted fs-3"></i>
                                </div>
                                <img id="editImagePreview"
                                     style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid #4f46e5;display:none">
                            @endif
                            <div>
                                <label class="btn btn-outline-primary btn-sm mb-1">
                                    <i class="bi bi-upload me-1"></i> {{ $menu->image ? 'Change Photo' : 'Upload Photo' }}
                                    <input type="file" name="image" id="editImageInput" class="d-none" accept="image/*">
                                </label>
                                <p class="text-muted small mb-0">JPG, PNG, GIF, WebP — max 2MB</p>
                            </div>
                        </div>
                        @if($menu->image)
                        <div class="form-check">
                            <input type="checkbox" name="remove_image" class="form-check-input" id="removeImage">
                            <label class="form-check-label small text-danger" for="removeImage">
                                <i class="bi bi-trash me-1"></i>Remove current photo
                            </label>
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="available" class="form-check-input" id="availableEdit" {{ old('available', $menu->available) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="availableEdit">Available for ordering</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                        <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('editImageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('editImagePreview');
            const placeholder = document.getElementById('editImagePlaceholder');
            preview.src = e.target.result;
            preview.style.display = '';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
