@extends('layouts.app')
@section('title', 'New Order')
@section('breadcrumb', 'Orders / New')

@push('styles')
<style>
    .menu-card { border-radius: .75rem; border: 2px solid #e2e8f0; transition: all .15s; cursor: pointer; }
    .menu-card:hover { border-color: #4f46e5; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79,70,229,.15); }
    .menu-card.selected { border-color: #4f46e5; background: #eef2ff; }
    .qty-badge { position: absolute; top: -8px; right: -8px; background: #4f46e5; color: #fff;
                 border-radius: 50%; width: 22px; height: 22px; font-size: .7rem;
                 display: flex; align-items: center; justify-content: center; font-weight: 700; }
    .order-item-row { background: #f8fafc; border-radius: .5rem; padding: .65rem .75rem; margin-bottom: .35rem; }
    #order-summary { position: sticky; top: calc(60px + 1.5rem); }
    .category-pills .btn { border-radius: 2rem; font-size: .8rem; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle-fill me-2 text-primary"></i>New Order</h1>
    <p>Select items from the menu, then fill in customer details.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small mb-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@if($menuItems->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        You have no available menu items. <a href="{{ route('menu.index') }}" class="alert-link">Add menu items first</a>.
    </div>
@else

<form method="POST" action="{{ route('orders.store') }}" id="orderForm">
    @csrf
    <!-- Hidden items will be injected here by JS -->
    <div id="hidden-items-container"></div>

    <div class="row g-4">
        <!-- Left: Menu picker -->
        <div class="col-lg-8">
            <!-- Category filter -->
            <div class="category-pills d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-primary btn-sm active" data-filter="all">All</button>
                @foreach($menuItems->keys() as $cat)
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-filter="{{ $cat }}">{{ $cat }}</button>
                @endforeach
            </div>

            @foreach($menuItems as $category => $items)
            <div class="category-section mb-3" data-category="{{ $category }}">
                <h6 class="text-muted fw-semibold mb-2 small text-uppercase">
                    <i class="bi bi-tag-fill me-1"></i>{{ $category }}
                </h6>
                <div class="row g-2">
                    @foreach($items as $item)
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="menu-card p-2 position-relative" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" onclick="toggleItem(this)">
                            <div class="qty-badge d-none" id="badge-{{ $item->id }}">1</div>
                            @if($item->image)
                                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                                     style="width:100%;height:70px;object-fit:cover;border-radius:.4rem;margin-bottom:.4rem">
                            @else
                                <div style="width:100%;height:70px;background:#f1f5f9;border-radius:.4rem;margin-bottom:.4rem;display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                            <div class="fw-medium small mb-1" style="line-height:1.2">{{ $item->name }}</div>
                            <div class="text-success fw-semibold small">₱{{ number_format($item->price, 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Right: Order summary + customer info -->
        <div class="col-lg-4">
            <div id="order-summary">
                <!-- Order Cart -->
                <div class="card mb-3">
                    <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="bi bi-cart3 me-2"></i>Order Cart</span>
                        <span class="badge bg-primary" id="item-count">0 items</span>
                    </div>
                    <div class="card-body px-4 pb-3" id="cart-body">
                        <p class="text-muted small text-center py-3" id="empty-cart-msg">
                            <i class="bi bi-cart-x d-block fs-3 mb-1"></i>
                            Click items to add them
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

                <!-- Customer Info -->
                <div class="card">
                    <div class="card-header py-3 px-4 fw-semibold">
                        <i class="bi bi-person-fill me-2"></i>Customer Info
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required placeholder="e.g. Juan Dela Cruz">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Table Number</label>
                            <input type="text" name="table_number" class="form-control" value="{{ old('table_number') }}" placeholder="e.g. Table 3">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-medium">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Special requests...">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold" id="placeOrderBtn" disabled>
                            <i class="bi bi-bag-check-fill me-1"></i> Place Order
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endif
@endsection

@push('scripts')
<script>
    // State: { itemId: { name, price, qty } }
    const cart = {};

    function toggleItem(card) {
        const id    = card.dataset.id;
        const name  = card.dataset.name;
        const price = parseFloat(card.dataset.price);

        if (cart[id]) {
            // Increase quantity
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
            const card = document.querySelector(`.menu-card[data-id="${id}"]`);
            card?.classList.remove('selected');
        }
        renderCart();
    }

    function renderCart() {
        const keys         = Object.keys(cart);
        const emptyMsg     = document.getElementById('empty-cart-msg');
        const cartItems    = document.getElementById('cart-items');
        const itemCount    = document.getElementById('item-count');
        const cartTotal    = document.getElementById('cart-total');
        const placeBtn     = document.getElementById('placeOrderBtn');
        const hiddenCont   = document.getElementById('hidden-items-container');

        emptyMsg.style.display  = keys.length ? 'none' : '';
        placeBtn.disabled       = keys.length === 0;

        // Update badges
        document.querySelectorAll('.qty-badge').forEach(b => b.classList.add('d-none'));
        keys.forEach(id => {
            const badge = document.getElementById('badge-' + id);
            if (badge) { badge.textContent = cart[id].qty; badge.classList.remove('d-none'); }
        });

        // Render cart rows
        cartItems.innerHTML = keys.map((id, i) => `
            <div class="order-item-row d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <div class="small fw-medium">${cart[id].name}</div>
                    <div class="text-success small">₱${(cart[id].price * cart[id].qty).toFixed(2)}</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="changeQty('${id}', -1)">
                        <i class="bi bi-dash"></i>
                    </button>
                    <span class="fw-semibold small" style="min-width:1.2rem;text-align:center">${cart[id].qty}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1" onclick="changeQty('${id}', 1)">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>
        `).join('');

        // Totals
        const total     = keys.reduce((s, id) => s + cart[id].price * cart[id].qty, 0);
        const totalItems = keys.reduce((s, id) => s + cart[id].qty, 0);
        cartTotal.textContent  = '₱' + total.toFixed(2);
        itemCount.textContent  = totalItems + ' item' + (totalItems !== 1 ? 's' : '');

        // Inject hidden inputs for form submission
        hiddenCont.innerHTML = keys.map((id, i) => `
            <input type="hidden" name="items[${i}][id]"  value="${id}">
            <input type="hidden" name="items[${i}][qty]" value="${cart[id].qty}">
        `).join('');
    }

    // Category filter
    document.querySelectorAll('.category-pills .btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.category-pills .btn').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');

            const filter = this.dataset.filter;
            document.querySelectorAll('.category-section').forEach(sec => {
                sec.style.display = (filter === 'all' || sec.dataset.category === filter) ? '' : 'none';
            });
        });
    });
</script>
@endpush
