<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    /**
     * Display a paginated, filterable list of all orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['items.menuItem', 'restaurantTable', 'payment']);

        // ── Date Filters ─────────────────────────────────────────────────────
        $period = $request->input('period', 'today');
        match ($period) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
            default => null,
        };

        // ── Type Filter ──────────────────────────────────────────────────────
        if ($type = $request->input('type')) {
            match ($type) {
                'qr'     => $query->qrOrders(),
                'manual' => $query->cashierOrders(),
                default  => null,
            };
        }

        // ── Status Filter ────────────────────────────────────────────────────
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // ── Payment Status Filter ─────────────────────────────────────────────
        if ($payStatus = $request->input('payment_status')) {
            if ($payStatus === 'unpaid') {
                $query->doesntHave('payment');
            } else {
                $query->whereHas('payment', fn ($q) => $q->where('status', $payStatus));
            }
        }

        // ── Search ────────────────────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('table_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('cashier.orders.index', compact('orders'));
    }

    /**
     * Show order detail (shared view).
     */
    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'restaurantTable', 'payment']);

        return view('cashier.orders.show', [
            'order'     => $order,
            'backRoute' => route('cashier.orders.index'),
            'backLabel' => 'Kelola Pesanan',
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $old = $order->status;
        $order->update(['status' => $request->status]);

        // If completing a dine-in order, free the table
        if ($request->status === 'completed' && $order->restaurant_table_id) {
            RestaurantTable::where('id', $order->restaurant_table_id)
                ->update(['status' => 'available']);
        }

        return redirect()
            ->route('cashier.orders.show', $order)
            ->with('success', "Status pesanan diperbarui menjadi {$order->fresh()->status_label}.");
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        $order->update(['status' => 'cancelled']);

        // Free the table if dine-in
        if ($order->restaurant_table_id) {
            RestaurantTable::where('id', $order->restaurant_table_id)
                ->update(['status' => 'available']);
        }

        return redirect()
            ->route('cashier.orders.index')
            ->with('success', "Pesanan {$order->invoice_number} berhasil dibatalkan.");
    }
}
