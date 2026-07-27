<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    /**
     * Show a printable receipt for the given order.
     */
    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'restaurantTable', 'payment']);

        return view('cashier.receipt', compact('order'));
    }
}
