<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the menu page.
     */
    public function index()
    {
        $menuItems = MenuItem::where('status', 'available')->get()->map(function ($item) {
            return [
                'id'          => $item->id,
                'name'        => $item->name,
                'image'       => $item->image,
                'imageAlt'    => $item->image_alt,
                'rating'      => $item->rating,
                'time'        => $item->time,
                'description' => $item->description,
                'price'       => (float) $item->price,
                'category'    => $item->category,
                'popular'     => $item->popular,
            ];
        })->toArray();

        // Kategori dari DB — hanya yang memiliki minimal 1 menu available
        $categories = Category::whereHas('menus', fn($q) => $q->where('status', 'available'))
            ->orderBy('name')
            ->get();

        return view('menu', [
            'menuItems'   => $menuItems,
            'tableNumber' => null,
            'categories'  => $categories,
            'taxRate'     => Setting::getTaxRate(),
        ]);
    }

    /**
     * Display the menu page for a specific table (scanned via QR code).
     * URL: /order/{table}  — {table} is the RestaurantTable ID.
     */
    public function showForTable(RestaurantTable $table)
    {
        // Jika meja disabled, tampilkan halaman tidak tersedia
        if ($table->status === 'disabled') {
            return response()->view('table-unavailable', [
                'tableNumber' => $table->table_number,
            ], 403);
        }

        $menuItems = MenuItem::where('status', 'available')
            ->get()
            ->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'image'       => $item->image,
                    'imageAlt'    => $item->image_alt,
                    'rating'      => $item->rating,
                    'time'        => $item->time,
                    'description' => $item->description,
                    'price'       => (float) $item->price,
                    'category'    => $item->category,
                    'popular'     => $item->popular,
                ];
            })
            ->toArray();

        // Kategori dari DB — hanya yang memiliki minimal 1 menu available
        $categories = Category::whereHas('menus', fn($q) => $q->where('status', 'available'))
            ->orderBy('name')
            ->get();

        return view('menu', [
            'menuItems'   => $menuItems,
            'tableNumber' => $table->table_number,
            'categories'  => $categories,
            'taxRate'     => Setting::getTaxRate(),
        ]);
    }
}
