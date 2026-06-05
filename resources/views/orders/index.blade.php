@extends('layouts.app')
@section('title', 'Orders')
@section('breadcrumb', 'Orders')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1><i class="bi bi-bag-check-fill me-2 text-primary"></i>Orders</h1>
        <p>Manage all customer orders.</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Order
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-3 px-4">
        <form method="GET" action="{{ route('orders.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-5">
                <label class="form-label small fw-medium mb-1">Search Customer</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Customer name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label small fw-medium mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','preparing','served','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Customer</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4"><span class="fw-semibold text-primary">#{{ $order->id }}</span></td>
                        <td class="fw-medium">{{ $order->customer_name }}</td>
                        <td class="text-muted small">{{ $order->table_number ?? '—' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $order->orderItems->count() }}</span></td>
                        <td class="fw-semibold text-success">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <!-- Quick status dropdown -->
                            <div class="dropdown">
                                <button class="badge bg-{{ $order->status_badge }} border-0 dropdown-toggle" style="cursor:pointer;font-size:.75rem" data-bs-toggle="dropdown">
                                    {{ ucfirst($order->status) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-sm shadow-sm">
                                    @foreach(['pending'=>'warning','preparing'=>'info','served'=>'success','cancelled'=>'danger'] as $status => $color)
                                    <li>
                                        <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="dropdown-item small {{ $order->status === $status ? 'fw-bold' : '' }}">
                                                <span class="badge bg-{{ $color }} me-1" style="width:8px;height:8px;padding:0;border-radius:50%;display:inline-block"></span>
                                                {{ ucfirst($status) }}
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}<br><span class="text-muted" style="font-size:.7rem">{{ $order->created_at->format('h:i A') }}</span></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('orders.receipt', $order) }}" class="btn btn-sm btn-outline-secondary me-1" title="Receipt" target="_blank">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-info me-1" title="View">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                onclick="confirmDelete('{{ route('orders.destroy', $order) }}')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x fs-2 d-block mb-2"></i>
                            @if(request()->hasAny(['status','search']))
                                No orders match your filter. <a href="{{ route('orders.index') }}">Clear filters</a>.
                            @else
                                No orders yet. <a href="{{ route('orders.create') }}">Create your first order</a>.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-transparent">{{ $orders->links() }}</div>
    @endif
</div>

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
function confirmDelete(action) {
    if (confirm('Delete this order? This cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = action;
        form.submit();
    }
}
</script>
@endpush
