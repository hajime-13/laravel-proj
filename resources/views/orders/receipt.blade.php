<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — Order #{{ $order->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', 'Courier New', monospace; background: #f1f5f9; display: flex; justify-content: center; padding: 2rem 1rem; }
        .receipt {
            background: #fff; width: 320px; padding: 1.5rem 1.25rem;
            border-radius: .5rem; box-shadow: 0 2px 16px rgba(0,0,0,.12);
        }
        .receipt-header { text-align: center; border-bottom: 2px dashed #e2e8f0; padding-bottom: 1rem; margin-bottom: 1rem; }
        .receipt-header h1 { font-size: 1.25rem; font-weight: 700; letter-spacing: .05em; }
        .receipt-header p { font-size: .75rem; color: #64748b; margin-top: .25rem; }
        .receipt-meta { font-size: .8rem; margin-bottom: 1rem; }
        .receipt-meta tr td { padding: .15rem 0; }
        .receipt-meta tr td:first-child { color: #64748b; width: 40%; }
        .receipt-meta tr td:last-child { font-weight: 600; }
        .items-header { display: flex; justify-content: space-between; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; border-bottom: 1px solid #e2e8f0; padding-bottom: .35rem; margin-bottom: .5rem; }
        .item-row { display: flex; justify-content: space-between; align-items: flex-start; font-size: .82rem; margin-bottom: .4rem; }
        .item-row .item-name { flex: 1; padding-right: .5rem; }
        .item-row .item-qty { color: #64748b; min-width: 2rem; text-align: center; }
        .item-row .item-price { min-width: 5rem; text-align: right; font-weight: 600; }
        .receipt-total { border-top: 2px dashed #e2e8f0; margin-top: 1rem; padding-top: .75rem; }
        .receipt-total .row { display: flex; justify-content: space-between; font-size: .875rem; margin-bottom: .25rem; }
        .receipt-total .grand { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .receipt-footer { text-align: center; font-size: .7rem; color: #94a3b8; margin-top: 1.25rem; border-top: 1px dashed #e2e8f0; padding-top: .75rem; }
        .status-badge {
            display: inline-block; padding: .2rem .6rem; border-radius: .25rem; font-size: .7rem; font-weight: 600;
            background: {{ match($order->status) { 'served'=>'#dcfce7', 'preparing'=>'#dbeafe', 'cancelled'=>'#fee2e2', default=>'#fef9c3' } }};
            color: {{ match($order->status) { 'served'=>'#166534', 'preparing'=>'#1e40af', 'cancelled'=>'#991b1b', default=>'#854d0e' } }};
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; border-radius: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <h1>📋 OrderList</h1>
        <p>{{ config('app.name') }}</p>
        <div class="mt-2">
            <span class="status-badge">{{ strtoupper($order->status) }}</span>
        </div>
    </div>

    <table class="receipt-meta w-100">
        <tr>
            <td>Order #</td>
            <td>#{{ $order->id }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td>Table</td>
            <td>{{ $order->table_number ?? '—' }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ $order->created_at->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td>Time</td>
            <td>{{ $order->created_at->format('h:i A') }}</td>
        </tr>
        @if($order->notes)
        <tr>
            <td>Notes</td>
            <td style="color:#64748b;font-style:italic">{{ $order->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="items-header">
        <span>Item</span>
        <span>Qty</span>
        <span>Amount</span>
    </div>

    @foreach($order->orderItems as $item)
    <div class="item-row">
        <div class="item-name">{{ $item->menuItem->name }}</div>
        <div class="item-qty">x{{ $item->quantity }}</div>
        <div class="item-price">₱{{ number_format($item->subtotal, 2) }}</div>
    </div>
    @endforeach

    <div class="receipt-total">
        <div class="row">
            <span>Subtotal</span>
            <span>₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
        <div class="row grand">
            <span>TOTAL</span>
            <span>₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="receipt-footer">
        <p>Thank you for your order!</p>
        <p>{{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <div class="no-print mt-3 d-flex gap-2">
        <button onclick="window.print()" style="flex:1;padding:.5rem;background:#4f46e5;color:#fff;border:none;border-radius:.4rem;cursor:pointer;font-weight:600;font-size:.875rem">
            🖨 Print Receipt
        </button>
        <a href="{{ route('orders.index') }}" style="flex:1;padding:.5rem;background:#f1f5f9;color:#1e293b;border:none;border-radius:.4rem;cursor:pointer;font-weight:600;font-size:.875rem;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center">
            ← Back
        </a>
    </div>
</div>
</body>
</html>
