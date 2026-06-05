<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalUsers      = User::count();
        $totalMenuItems  = MenuItem::where('user_id', $userId)->count();
        $totalOrders     = Order::where('user_id', $userId)->count();
        $totalRevenue    = Order::where('user_id', $userId)
                                ->where('status', 'served')
                                ->sum('total_amount');

        // Orders by status for doughnut chart
        $ordersByStatus = Order::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Orders per day (last 7 days) for line chart
        $ordersPerDay = Order::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Fill missing days
        $labels     = [];
        $orderCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = now()->subDays($i)->format('Y-m-d');
            $labels[]      = now()->subDays($i)->format('M d');
            $orderCounts[] = $ordersPerDay[$date] ?? 0;
        }

        // Top menu items by order quantity
        $topItems = OrderItem::query()
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::where('user_id', $userId)
            ->with('orderItems')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalUsers', 'totalMenuItems', 'totalOrders', 'totalRevenue',
            'ordersByStatus', 'labels', 'orderCounts',
            'topItems', 'recentOrders'
        ));
    }
}
