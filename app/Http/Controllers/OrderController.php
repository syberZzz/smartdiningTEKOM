<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Store a new order.
     * Expects JSON: { table_number, notes?, items: [{ menu_item_id, quantity }] }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_number'          => 'required|string|max:10',
            'notes'                 => 'nullable|string|max:500',
            'items'                 => 'required|array|min:1',
            'items.*.menu_item_id'  => 'required|integer|exists:menu_items,id',
            'items.*.quantity'      => 'required|integer|min:1|max:99',
            'items.*.notes'         => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $order = DB::transaction(function () use ($validated) {
                // Cari RestaurantTable berdasarkan table_number untuk mendapatkan ID
                $restaurantTable = RestaurantTable::where('table_number', $validated['table_number'])->first();

                // Tolak order jika meja disabled
                if ($restaurantTable && $restaurantTable->status === 'disabled') {
                    throw new \Exception('Table ' . $validated['table_number'] . ' is not active. Please contact staff.');
                }

                $order = Order::create([
                    'table_number'        => $validated['table_number'],
                    'restaurant_table_id' => $restaurantTable?->id,
                    'order_type'          => 'self',
                    'notes'               => $validated['notes'] ?? null,
                    'status'              => 'pending',
                    'subtotal'            => 0,
                    'tax_percentage'      => 0,
                    'tax_amount'          => 0,
                    'total_price'         => 0,
                ]);

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

                $order->update([
                    'subtotal'       => $subtotal,
                    'tax_percentage' => $taxRate,
                    'tax_amount'     => $taxAmount,
                    'total_price'    => $subtotal + $taxAmount,
                ]);

                return $order;
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'order_id' => $order->id,
            'redirect' => route('orders.show', $order->id),
        ]);
    }

    /**
     * Show order confirmation / tracking page.
     */
    public function show(Order $order)
    {
        $order->load('items.menuItem', 'restaurantTable');

        // Tentukan URL "Kembali ke Menu" — jika order dari QR scan, kembali ke halaman meja
        $backUrl = $order->restaurantTable
            ? route('order.table', $order->restaurantTable)
            : route('menu.index');

        return view('order-confirmation', [
            'order'   => $order,
            'backUrl' => $backUrl,
        ]);
    }
}
