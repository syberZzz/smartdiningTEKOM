<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Payment;

class OrderSeeder extends Seeder
{
    /**
     * Seed sample orders with order items and payments for dashboard/reports.
     */
    public function run(): void
    {
        $menuItems = MenuItem::all();

        if ($menuItems->isEmpty()) {
            return;
        }

        $statuses    = ['completed', 'completed', 'completed', 'pending', 'preparing', 'ready'];
        $orderTypes  = ['self', 'self', 'self', 'cashier'];
        $payMethods  = ['cash', 'cash', 'qris'];
        $tables      = ['01', '02', '03', '04', '05', '06', '07', '08'];

        // Generate orders across the last 30 days
        for ($day = 0; $day < 30; $day++) {
            $date = now()->subDays($day);
            $ordersPerDay = rand(3, 8);

            for ($o = 0; $o < $ordersPerDay; $o++) {
                $status    = $statuses[array_rand($statuses)];
                $orderType = $orderTypes[array_rand($orderTypes)];
                $table     = $tables[array_rand($tables)];
                $hour      = rand(10, 21);
                $minute    = rand(0, 59);

                $orderDate = $date->copy()->setTime($hour, $minute);

                $order = Order::create([
                    'table_number' => $table,
                    'status'       => $status,
                    'order_type'   => $orderType,
                    'total_price'  => 0,
                    'notes'        => null,
                    'created_at'   => $orderDate,
                    'updated_at'   => $orderDate,
                ]);

                $totalPrice = 0;
                $itemCount  = rand(1, 4);
                $usedItems  = [];

                for ($i = 0; $i < $itemCount; $i++) {
                    $menuItem = $menuItems->random();

                    // Avoid duplicates in same order
                    if (in_array($menuItem->id, $usedItems)) {
                        continue;
                    }
                    $usedItems[] = $menuItem->id;

                    $qty      = rand(1, 3);
                    $subtotal = $menuItem->price * $qty;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'quantity'     => $qty,
                        'price'        => $menuItem->price,
                        'subtotal'     => $subtotal,
                        'created_at'   => $orderDate,
                        'updated_at'   => $orderDate,
                    ]);

                    $totalPrice += $subtotal;
                }

                $order->update(['total_price' => $totalPrice]);

                // Create payment for completed orders
                if ($status === 'completed') {
                    Payment::create([
                        'order_id'   => $order->id,
                        'method'     => $payMethods[array_rand($payMethods)],
                        'amount'     => $totalPrice,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }
            }
        }
    }
}
