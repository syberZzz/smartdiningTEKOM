<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class QrOrderController extends Controller
{
    /**
     * List all QR orders with optional filters.
     */
    public function index(Request $request)
    {
        $query = Order::with(['items.menuItem', 'restaurantTable', 'payment'])
            ->qrOrders();

        // ── Filters ──────────────────────────────────────────────────────────
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->input('payment_status')) {
            $query->whereHas('payment', fn ($q) => $q->where('status', $paymentStatus));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('table_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Date filters
        $date = $request->input('date', 'today');
        match ($date) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
            default => null,
        };

        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'pending'   => Order::qrOrders()->today()->where('status', 'pending')->count(),
            'preparing' => Order::qrOrders()->today()->where('status', 'preparing')->count(),
        ];

        return view('cashier.qr-orders.index', compact('orders', 'stats'));
    }

    /**
     * Show detail of a single QR order.
     */
    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'restaurantTable', 'payment']);

        return view('cashier.orders.show', [
            'order'      => $order,
            'backRoute'  => route('cashier.qr-orders.index'),
            'backLabel'  => 'QR Orders',
        ]);
    }

    /**
     * Confirm a pending order.
     */
    public function confirm(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan dengan status menunggu yang bisa dikonfirmasi.');
        }

        $order->update(['status' => 'preparing']);

        return back()->with('success', "Pesanan {$order->invoice_number} dikonfirmasi.");
    }

    /**
     * Update status of an order.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update(['status' => $request->status]);

        return back()->with('success', "Status pesanan {$order->invoice_number} diperbarui menjadi {$order->fresh()->status_label}.");
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

        return back()->with('success', "Pesanan {$order->invoice_number} berhasil dibatalkan.");
    }

    /**
     * JSON endpoint for polling new orders (for auto-refresh).
     */
    public function poll(Request $request)
    {
        $since = $request->input('since');

        $query = Order::with(['items', 'restaurantTable'])
            ->qrOrders()
            ->whereIn('status', ['pending', 'preparing']);

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $orders = $query->orderByDesc('created_at')->limit(20)->get()
            ->map(fn ($o) => [
                'id'             => $o->id,
                'invoice_number' => $o->invoice_number,
                'table_number'   => $o->table_number,
                'customer_name'  => $o->customer_name,
                'total_items'    => $o->total_items,
                'total_price'    => $o->total_price,
                'status'         => $o->status,
                'status_label'   => $o->status_label,
                'created_at'     => $o->created_at->toIso8601String(),
                'created_ago'    => $o->created_at->diffForHumans(),
            ]);

        $pendingCount = Order::qrOrders()->where('status', 'pending')->count();

        return response()->json([
            'orders'        => $orders,
            'pending_count' => $pendingCount,
            'server_time'   => now()->toIso8601String(),
        ]);
    }
}
