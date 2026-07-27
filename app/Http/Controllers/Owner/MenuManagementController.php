<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuManagementController extends Controller
{
    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        $query = MenuItem::with('categoryRelation');

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $menus      = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('owner.menus.index', compact('menus', 'categories'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('owner.menus.create', compact('categories'));
    }

    /**
     * Store a newly created menu item.
     */
    public function store(StoreMenuRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-images', 'public');
            $data['image'] = '/storage/' . $path;
            $data['image_alt'] = $data['name'];
        } else {
            $data['image'] = null; // nullable — no image uploaded
        }

        // Set the category string from the category relationship
        $category = Category::find($data['category_id']);
        $data['category'] = strtolower($category->name);
        $data['popular'] = $request->boolean('popular');

        MenuItem::create($data);

        return redirect()->route('owner.menus.index')
            ->with('success', 'Item menu berhasil ditambahkan.');
    }

    /**
     * Display the specified menu item.
     */
    public function show(MenuItem $menu)
    {
        $menu->load('categoryRelation', 'orderItems');

        $totalSold = $menu->orderItems()->sum('quantity');
        $totalRevenue = $menu->orderItems()->sum('subtotal');

        return view('owner.menus.show', compact('menu', 'totalSold', 'totalRevenue'));
    }

    /**
     * Show the form for editing a menu item.
     */
    public function edit(MenuItem $menu)
    {
        $categories = Category::orderBy('name')->get();
        return view('owner.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified menu item.
     */
    public function update(UpdateMenuRequest $request, MenuItem $menu)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it's a local file
            if ($menu->image && str_starts_with($menu->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $menu->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('menu-images', 'public');
            $data['image'] = '/storage/' . $path;
            $data['image_alt'] = $data['name'];
        }

        // Set the category string from the category relationship
        $category = Category::find($data['category_id']);
        $data['category'] = strtolower($category->name);
        $data['popular'] = $request->boolean('popular');

        $menu->update($data);

        return redirect()->route('owner.menus.index')
            ->with('success', 'Item menu berhasil diperbarui.');
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy(MenuItem $menu)
    {
        // Delete image if local
        if ($menu->image && str_starts_with($menu->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $menu->image);
            Storage::disk('public')->delete($oldPath);
        }

        $menu->delete();

        return redirect()->route('owner.menus.index')
            ->with('success', 'Item menu berhasil dihapus.');
    }

    /**
     * Toggle menu item availability status.
     */
    public function toggleStatus(MenuItem $menu)
    {
        $menu->update([
            'status' => $menu->status === 'available' ? 'unavailable' : 'available',
        ]);

        return redirect()->back()
            ->with('success', "Status menu \"{$menu->name}\" berhasil diperbarui.");
    }
}
