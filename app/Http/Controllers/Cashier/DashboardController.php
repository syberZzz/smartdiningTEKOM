<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // ── Summary Cards ─────────────────────────────────────────────────────
        $newOrders = Order::today()->where('status', 'pending')->count();

        $inProcess = Order::today()
            ->where('status', 'preparing')
            ->count();

        $completedToday = Order::today()->where('status', 'completed')->count();

        $todayRevenue = Order::today()
            ->where('status', 'completed')
            ->sum('total_price');

        // ── Order Counts by Type ──────────────────────────────────────────────
        $qrOrdersToday     = Order::today()->qrOrders()->count();
        $manualOrdersToday = Order::today()->cashierOrders()->count();
        $pendingOrders     = Order::today()->where('status', 'pending')->count();

        // ── Recent Orders ─────────────────────────────────────────────────────
        $recentOrders = Order::with(['items.menuItem', 'payment', 'restaurantTable'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ── Incoming QR Orders (pending/confirmed) ────────────────────────────
        $incomingQrOrders = Order::with(['items', 'restaurantTable'])
            ->qrOrders()
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Recent Manual Orders ──────────────────────────────────────────────
        $recentManualOrders = Order::with(['items', 'payment'])
            ->cashierOrders()
            ->today()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('cashier.dashboard', compact(
            'newOrders',
            'inProcess',
            'completedToday',
            'todayRevenue',
            'qrOrdersToday',
            'manualOrdersToday',
            'pendingOrders',
            'recentOrders',
            'incomingQrOrders',
            'recentManualOrders',
        ));
    }
}
