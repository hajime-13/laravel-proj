<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('orderItems.menuItem')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('customer_name', 'like', '%' . $request->search . '%');
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $menuItems = MenuItem::where('user_id', Auth::id())
            ->where('available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('orders.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number'  => 'nullable|string|max:50',
            'notes'         => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|exists:menu_items,id',
            'items.*.qty'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id'       => Auth::id(),
                'customer_name' => $request->customer_name,
                'table_number'  => $request->table_number,
                'notes'         => $request->notes,
                'status'        => 'pending',
                'total_amount'  => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $menuItem = MenuItem::findOrFail($item['id']);
                $subtotal = $menuItem->price * $item['qty'];
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $item['qty'],
                    'unit_price'   => $menuItem->price,
                    'subtotal'     => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $total]);
        });

        return redirect()->route('orders.index')
            ->with('toast_success', 'Order for "' . $request->customer_name . '" placed successfully.');
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('orderItems.menuItem');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('orderItems.menuItem');

        $menuItems = MenuItem::where('user_id', Auth::id())
            ->where('available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $existingItems = $order->orderItems->map(function ($oi) {
            return [
                'id'    => $oi->menu_item_id,
                'name'  => $oi->menuItem->name,
                'price' => (float) $oi->unit_price,
                'qty'   => $oi->quantity,
            ];
        })->values();

        return view('orders.edit', compact('order', 'menuItems', 'existingItems'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number'  => 'nullable|string|max:50',
            'status'        => 'required|in:pending,preparing,served,cancelled',
            'notes'         => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|exists:menu_items,id',
            'items.*.qty'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $order) {
            $order->orderItems()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $menuItem = MenuItem::findOrFail($item['id']);
                $subtotal = $menuItem->price * $item['qty'];
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $item['qty'],
                    'unit_price'   => $menuItem->price,
                    'subtotal'     => $subtotal,
                ]);
            }

            $order->update([
                'customer_name' => $request->customer_name,
                'table_number'  => $request->table_number,
                'status'        => $request->status,
                'notes'         => $request->notes,
                'total_amount'  => $total,
            ]);
        });

        return redirect()->route('orders.index')
            ->with('toast_success', 'Order #' . $order->id . ' updated successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'status' => 'required|in:pending,preparing,served,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('toast_success', 'Order #' . $order->id . ' marked as ' . ucfirst($request->status) . '.');
    }

    public function destroy(Order $order)
    {
        $this->authorizeOrder($order);
        $id = $order->id;
        $order->delete();

        return redirect()->route('orders.index')
            ->with('toast_danger', 'Order #' . $id . ' deleted.');
    }

    public function receipt(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('orderItems.menuItem');
        return view('orders.receipt', compact('order'));
    }

    private function authorizeOrder(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
