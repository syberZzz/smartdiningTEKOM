<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreManualOrderRequest;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class ManualOrderController extends Controller
{
    /**
     * Show the POS order creation page.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        $menuItems = MenuItem::with('categoryRelation')
            ->where('status', 'available')
            ->orderBy('name')
            ->get();

        $tables = RestaurantTable::where('status', '!=', 'disabled')
            ->orderBy('table_number')
            ->get();

        return view('cashier.orders.create', compact('categories', 'menuItems', 'tables'));
    }

    /**
     * Create a new manual order with payment.
     */
    public function store(StoreManualOrderRequest $request)
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($validated, $request) {
            // Resolve table number
            $tableNumber = 'Takeaway';
            $tableId     = null;

            if ($validated['order_type'] === 'dine_in' && !empty($validated['restaurant_table_id'])) {
                $table       = RestaurantTable::findOrFail($validated['restaurant_table_id']);

                if ($table->status === 'disabled') {
                    throw new \Exception('Table ' . $table->table_number . ' is disabled and cannot be used for orders.');
                }

                $tableNumber = $table->table_number;
                $tableId     = $table->id;

            }

            // Create order
            $order = Order::create([
                'customer_name'       => $validated['customer_name'] ?? null,
                'table_number'        => $tableNumber,
                'restaurant_table_id' => $tableId,
                'status'              => 'preparing', // manual orders start as preparing
                'order_type'          => 'cashier',
                'subtotal'            => 0,
                'tax_percentage'      => 0,
                'tax_amount'          => 0,
                'total_price'         => 0,
                'notes'               => null,
            ]);

            // Create order items
            $subtotal = 0;

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $itemSubtotal = $menuItem->price * $itemData['quantity'];

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $itemData['quantity'],
                    'notes'        => $itemData['notes'] ?? null,
                    'price'        => $menuItem->price,
                    'subtotal'     => $itemSubtotal,
                ]);

                $subtotal += $itemSubtotal;
            }

            // Calculate PPN
            $taxRate   = Setting::getTaxRate();
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $grandTotal = $subtotal + $taxAmount;

            $order->update([
                'subtotal'       => $subtotal,
                'tax_percentage' => $taxRate,
                'tax_amount'     => $taxAmount,
                'total_price'    => $grandTotal,
            ]);

            // Create payment
            $method       = $validated['payment_method'];
            $cashAmount   = $method === 'cash' ? (float) ($validated['cash_amount'] ?? 0) : $grandTotal;
            $changeAmount = $method === 'cash' ? max(0, $cashAmount - $grandTotal) : 0;
            $payStatus    = 'paid'; // manual orders are paid immediately

            Payment::create([
                'order_id'      => $order->id,
                'method'        => $method,
                'amount'        => $cashAmount,
                'status'        => $payStatus,
                'change_amount' => $changeAmount,
            ]);

            return $order;
        });

        if ($request->wantsJson()) {
            session()->flash('success', "Pesanan {$order->invoice_number} berhasil dibuat.");
            return response()->json([
                'redirect' => route('cashier.orders.show', $order)
            ]);
        }

        return redirect()
            ->route('cashier.orders.show', $order)
            ->with('success', "Pesanan {$order->invoice_number} berhasil dibuat.");
    }
}
