<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RestaurantTable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports page.
     */
    public function index(Request $request)
    {
        $today     = now()->toDateString();
        $startDate = $request->input('start_date', $today);
        $endDate   = $request->input('end_date', $today);
        $period    = $request->input('period', 'today');

        // Adjust dates based on period
        switch ($period) {
            case 'weekly':
                $startDate = now()->startOfWeek()->toDateString();
                $endDate   = now()->endOfWeek()->toDateString();
                break;
            case 'monthly':
                $startDate = now()->startOfMonth()->toDateString();
                $endDate   = now()->endOfMonth()->toDateString();
                break;
            case 'custom':
                // Use provided dates
                break;
            default: // today
                $startDate = $today;
                $endDate   = $today;
                break;
        }

        // Dashboard Summary
        $summary = $this->getDashboardSummary($startDate, $endDate);

        // Daily Report
        $dailyReport = $this->getDailyReport($startDate, $endDate);

        // Monthly Report (current year)
        $monthlyReport = $this->getMonthlyReport();

        // Best Selling Menu
        $bestSelling = $this->getBestSellingMenu($startDate, $endDate);

        // Category Report
        $categoryReport = $this->getCategoryReport($startDate, $endDate);


        // Payment Summary
        $paymentSummary = $this->getPaymentSummary($startDate, $endDate);

        // Payment Status
        $paymentStatus  = $this->getPaymentStatusReport($startDate, $endDate);

        return view('owner.reports.index', compact(
            'summary',
            'dailyReport',
            'monthlyReport',
            'bestSelling',
            'categoryReport',
            'paymentSummary',
            'paymentStatus',
            'startDate',
            'endDate',
            'period',
        ));
    }

    /**
     * Get chart data for AJAX requests.
     */
    public function chartData(Request $request)
    {
        $type      = $request->input('type', 'revenue');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $data = match ($type) {
            'revenue'   => $this->getRevenueChartData($startDate, $endDate),
            'orders'    => $this->getOrdersChartData($startDate, $endDate),
            'customers' => $this->getCustomersChartData($startDate, $endDate),
            default     => [],
        };

        return response()->json($data);
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $data = [
            'summary'        => $this->getDashboardSummary($startDate, $endDate),
            'bestSelling'    => $this->getBestSellingMenu($startDate, $endDate),
            'categoryReport' => $this->getCategoryReport($startDate, $endDate),
            'paymentSummary' => $this->getPaymentSummary($startDate, $endDate),
            'paymentStatus'  => $this->getPaymentStatusReport($startDate, $endDate),
            'startDate'      => $startDate,
            'endDate'        => $endDate,
        ];

        $pdf = Pdf::loadView('owner.reports.pdf', $data);

        return $pdf->download("report-{$startDate}-to-{$endDate}.pdf");
    }

    // ─── Private Helper Methods ───

    private function getDashboardSummary(string $startDate, string $endDate): array
    {
        $completedOrders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed');

        $allOrders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        return [
            'revenue'         => (clone $completedOrders)->sum('total_price'),
            'total_tax'       => (clone $completedOrders)->sum('tax_amount'),
            'orders'          => (clone $allOrders)->count(),
            'customers'       => (clone $allOrders)->distinct('table_number')->count('table_number'),
            'avg_transaction' => (clone $completedOrders)->avg('total_price') ?? 0,
            'active_tables'   => RestaurantTable::where('status', 'available')->count(),
        ];
    }

    private function getDailyReport(string $startDate, string $endDate): object
    {
        return Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total_price ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(CASE WHEN order_type = "self" THEN 1 ELSE 0 END) as self_orders'),
                DB::raw('SUM(CASE WHEN order_type = "cashier" THEN 1 ELSE 0 END) as cashier_orders')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    private function getMonthlyReport(): object
    {
        return Order::whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total_price ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('COUNT(DISTINCT table_number) as customers')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
    }

    private function getBestSellingMenu(string $startDate, string $endDate): object
    {
        return DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'menu_items.id',
                'menu_items.name',
                'menu_items.image',
                'menu_items.category',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.image', 'menu_items.category')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    private function getCategoryReport(string $startDate, string $endDate): object
    {
        return DB::table('categories')
            ->leftJoin('menu_items', 'categories.id', '=', 'menu_items.category_id')
            ->leftJoin('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('orders.status', '!=', 'cancelled');
            })
            ->select(
                'categories.name as category',
                DB::raw('COUNT(DISTINCT menu_items.id) as total_menu'),
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }





    private function getPaymentStatusReport(string $startDate, string $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('status')
            ->get();

        return [
            'paid' => $orders->where('status', 'completed')->count(),
            'pay_at_cashier' => $orders->whereIn('status', ['pending', 'preparing'])->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];
    }

    private function getPaymentSummary(string $startDate, string $endDate): array
    {
        $payments = Payment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('method')
            ->get();

        $cash = $payments->firstWhere('method', 'cash');
        $digitalCount = $payments->whereIn('method', ['qris', 'e-wallet', 'card'])->sum('count');
        $digitalTotal = $payments->whereIn('method', ['qris', 'e-wallet', 'card'])->sum('total');

        return [
            'cash' => [
                'count' => $cash?->count ?? 0,
                'total' => $cash?->total ?? 0,
            ],
            'digital' => [
                'count' => $digitalCount,
                'total' => $digitalTotal,
            ],
        ];
    }

    private function getRevenueChartData(string $startDate, string $endDate): array
    {
        $data = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data'   => $data->pluck('revenue')->toArray(),
        ];
    }

    private function getOrdersChartData(string $startDate, string $endDate): array
    {
        $data = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data'   => $data->pluck('orders')->toArray(),
        ];
    }

    private function getCustomersChartData(string $startDate, string $endDate): array
    {
        $data = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT table_number) as customers')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data'   => $data->pluck('customers')->toArray(),
        ];
    }
}
