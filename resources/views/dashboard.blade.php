@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Dashboard</h1>
    <p>Welcome back, {{ Auth::user()->name }}! Here's your overview.</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <p>Total Users</p>
            <h3>{{ $totalUsers }}</h3>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
            <p>Menu Items</p>
            <h3>{{ $totalMenuItems }}</h3>
            <i class="bi bi-menu-button-wide-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <p>Total Orders</p>
            <h3>{{ $totalOrders }}</h3>
            <i class="bi bi-bag-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <p>Revenue (Served)</p>
            <h3>₱{{ number_format($totalRevenue, 2) }}</h3>
            <i class="bi bi-currency-dollar stat-icon"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Orders per day -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                <span><i class="bi bi-bar-chart-fill text-primary me-2"></i>Orders (Last 7 Days)</span>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="ordersLineChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Orders by status doughnut -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3 px-4">
                <span><i class="bi bi-pie-chart-fill text-warning me-2"></i>Orders by Status</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusDoughnut" style="max-height:220px;max-width:220px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Top menu items -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3 px-4">
                <span><i class="bi bi-trophy-fill text-warning me-2"></i>Top Ordered Items</span>
            </div>
            <div class="card-body px-4 pb-4">
                @if($topItems->isEmpty())
                    <p class="text-muted small text-center py-3">No orders yet.</p>
                @else
                    <canvas id="topItemsBar" height="160"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent orders -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                <span><i class="bi bi-clock-history text-info me-2"></i>Recent Orders</span>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->isEmpty())
                    <p class="text-muted small text-center py-4">No orders yet. <a href="{{ route('orders.create') }}">Create one</a>.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $order->customer_name }}</td>
                                    <td>{{ $order->orderItems->count() }} item(s)</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Line chart - orders per day
    new Chart(document.getElementById('ordersLineChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Orders',
                data: @json($orderCounts),
                fill: true,
                tension: 0.4,
                backgroundColor: 'rgba(79,70,229,.1)',
                borderColor: '#4f46e5',
                borderWidth: 2.5,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Doughnut chart - orders by status
    const statusData = @json($ordersByStatus);
    const statusColors = { pending:'#f59e0b', preparing:'#3b82f6', served:'#22c55e', cancelled:'#ef4444' };
    new Chart(document.getElementById('statusDoughnut'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: Object.keys(statusData).map(k => statusColors[k] ?? '#94a3b8'),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 } } } },
            cutout: '65%',
        }
    });

    // Bar chart - top items
    @if($topItems->isNotEmpty())
    new Chart(document.getElementById('topItemsBar'), {
        type: 'bar',
        data: {
            labels: @json($topItems->pluck('name')),
            datasets: [{
                label: 'Qty Ordered',
                data: @json($topItems->pluck('total_qty')),
                backgroundColor: ['#4f46e5','#7c3aed','#0ea5e9','#22c55e','#f59e0b'],
                borderRadius: 6,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
</script>
@endpush
