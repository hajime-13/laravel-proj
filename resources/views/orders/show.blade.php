@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('breadcrumb', 'Orders / #' . $order->id)

@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1><i class="bi bi-receipt me-2 text-primary"></i>Order #{{ $order->id }}</h1>
        <p>{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('orders.receipt', $order) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-printer-fill me-1"></i> Receipt
        </a>
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil-fill me-1"></i> Edit
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3 px-4 fw-semibold">
                <i class="bi bi-list-ul me-2"></i>Order Items
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $item->menuItem->name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end text-muted">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end pe-4 fw-semibold">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end ps-4">Total</th>
                            <th class="text-end pe-4 text-success fs-6">₱{{ number_format($order->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-3 px-4 fw-semibold">
                <i class="bi bi-info-circle me-2"></i>Order Details
            </div>
            <div class="card-body px-4">
                <dl class="row mb-0">
                    <dt class="col-5 small text-muted">Customer</dt>
                    <dd class="col-7 fw-medium">{{ $order->customer_name }}</dd>

                    <dt class="col-5 small text-muted">Table</dt>
                    <dd class="col-7">{{ $order->table_number ?? '—' }}</dd>

                    <dt class="col-5 small text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $order->status_badge }} fs-6">{{ ucfirst($order->status) }}</span>
                    </dd>

                    <dt class="col-5 small text-muted">Notes</dt>
                    <dd class="col-7 text-muted small">{{ $order->notes ?: '—' }}</dd>

                    <dt class="col-5 small text-muted">Created</dt>
                    <dd class="col-7 small text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
