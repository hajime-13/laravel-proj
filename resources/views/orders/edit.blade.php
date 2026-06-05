@extends('layouts.app')
@section('title', 'Edit Order #' . $order->id)
@section('breadcrumb', 'Orders / Edit #' . $order->id)

@push('styles')
<style>
    .menu-card { border-radius:.75rem; border:2px solid #e2e8f0; transition:all .15s; cursor:pointer; }
    .menu-card:hover { border-color:#4f46e5; transform:translateY(-2px); box-shadow:0 4px 12px rgba(79,70,229,.15); }
    .menu-card.selected { border-color:#4f46e5; background:#eef2ff; }
    .qty-badge { position:absolute; top:-8px; right:-8px; background:#4f46e5; color:#fff;
                 border-radius:50%; width:22px; height:22px; font-size:.7rem;
                 display:flex; align-items:center; justify-content:center; font-weight:700; }
    .order-item-row { background:#f8fafc; border-radius:.5rem; padding:.65rem .75rem; margin-bottom:.35rem; }
    #order-summary { position:sticky; top:calc(60px + 1.5rem); }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Order #{{ $order->id }}</h1>
    <p>Modify items or details for this order.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small mb-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('orders.update', $order) }}" id="orderForm">
    @csrf @method('PUT')
    <div id="hidden-items-container"></div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-2">
                @foreach($menuItems as $category => $items)
                <div class="col-12">
                    <h6 class="text-muted fw-semibold mb-2 small text-uppercase">
                        <i class="bi bi-tag-fill me-1"></i>{{ $category }}
                    </h6>
                    <div class="row g-2">
                        @foreach($items as $item)
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="menu-card p-2 position-relative"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}"
                                onclick="toggleItem(this)">
                                <div class="qty-badge d-none" id="badge-{{ $item->id }}">1</div>
                                <div class="fw-medium small">{{ $item->name }}</div>
                                <div class="text-success small">₱{{ number_format($item->price, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div id="order-summary">
                <div class="card mb-3">
                    <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="bi bi-cart3 me-2"></i>Cart</span>
                        <span class="badge bg-primary" id="item-count">0 items</span>
                    </div>
                    <div class="card-body px-4 pb-3">
                        <p class="text-muted small text-center py-3" id="empty-cart-msg" style="display:none">
                            <i class="bi bi-cart-x d-block fs-3 mb-1"></i>Click items to add
                        </p>
                        <div id="cart-items"></div>
                    </div>
                    <div class="card-footer bg-transparent px-4 py-3">
                        <div class="d-flex justify-content-between fw-semibold">
                            <span>Total</span>
                            <span class="text-success fs-5" id="cart-total">₱0.00</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header py-3 px-4 fw-semibold">
                        <i class="bi bi-person-fill me-2"></i>Details
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $order->customer_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Table Number</label>
                            <input type="text" name="table_number" class="form-control" value="{{ old('table_number', $order->table_number) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','preparing','served','cancelled'] as $status)
                                <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-medium">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold" id="placeOrderBtn" disabled>
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const cart = {};

    // Pre-populate cart from existing order items
    const existingItems = @json($order->orderItems->map(fn($oi) => ['id' => $oi->menu_item_id, 'name' => $oi->menuItem->name, 'price' => (float)$oi->unit_price, 'qty' => $oi->quantity]));
    existingItems.forEach(item => {
        cart[item.id] = { name: item.name, price: item.price, qty: item.qty };
    });
    renderCart();

    function toggleItem(card) {
        const id    = card.dataset.id;
        const name  = card.dataset.name;
        const price = parseFloat(card.dataset.price);
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = { name, price, qty: 1 };
            card.classList.add('selected');
        }
        renderCart();
    }

    function changeQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
            document.querySelector(`.menu-card[data-id="${id}"]`)?.classList.remove('selected');
        }
        renderCart();
    }

    function renderCart() {
        const keys       = Object.keys(cart);
        document.getElementById('empty-cart-msg').style.display = keys.length ? 'none' : '';
        document.getElementById('placeOrderBtn').disabled        = keys.length === 0;

        document.querySelectorAll('.qty-badge').forEach(b => b.classList.add('d-none'));
        document.querySelectorAll('.menu-card').forEach(c => c.classList.remove('selected'));
        keys.forEach(id => {
            const badge = document.getElementById('badge-' + id);
            if (badge) { badge.textContent = cart[id].qty; badge.classList.remove('d-none'); }
            document.querySelector(`.menu-card[data-id="${id}"]`)?.classList.add('selected');
        });

        document.getElementById('cart-items').innerHTML = keys.map(id => `
            <div class="order-item-row d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <div class="small fw-medium">${cart[id].name}</div>
                    <div class="text-success small">₱${(cart[id].price * cart[id].qty).toFixed(2)}</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="changeQty('${id}', -1)"><i class="bi bi-dash"></i></button>
                    <span class="fw-semibold small" style="min-width:1.2rem;text-align:center">${cart[id].qty}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1" onclick="changeQty('${id}', 1)"><i class="bi bi-plus"></i></button>
                </div>
            </div>
        `).join('');

        const total      = keys.reduce((s, id) => s + cart[id].price * cart[id].qty, 0);
        const totalItems = keys.reduce((s, id) => s + cart[id].qty, 0);
        document.getElementById('cart-total').textContent = '₱' + total.toFixed(2);
        document.getElementById('item-count').textContent = totalItems + ' item' + (totalItems !== 1 ? 's' : '');

        document.getElementById('hidden-items-container').innerHTML = keys.map((id, i) => `
            <input type="hidden" name="items[${i}][id]"  value="${id}">
            <input type="hidden" name="items[${i}][qty]" value="${cart[id].qty}">
        `).join('');
    }
</script>
@endpush
