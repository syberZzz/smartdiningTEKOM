<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the owner dashboard.
     */
    public function index()
    {
        $today = now()->toDateString();

        // Today's statistics
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_price');

        $todayOrders = Order::whereDate('created_at', $today)->count();

        // Customers today = unique table_numbers that ordered today
        $todayCustomers = Order::whereDate('created_at', $today)
            ->distinct('table_number')
            ->count('table_number');

        // Average transaction
        $avgTransaction = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->avg('total_price') ?? 0;

        // Active tables
        $activeTables = RestaurantTable::where('status', 'available')->count();
        $totalTables  = RestaurantTable::count();

        // Best selling menu (all time, top 5)
        $bestSelling = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'menu_items.id',
                'menu_items.name',
                'menu_items.image',
                'menu_items.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.image', 'menu_items.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent activity (latest 10 orders)
        $recentOrders = Order::with('items.menuItem')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('owner.dashboard', compact(
            'todayRevenue',
            'todayOrders',
            'todayCustomers',
            'avgTransaction',
            'activeTables',
            'totalTables',
            'bestSelling',
            'recentOrders',
        ));
    }
}
