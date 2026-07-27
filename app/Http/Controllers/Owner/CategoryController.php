<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('menus');

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('owner.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('owner.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['icon'] = $data['icon'] ?? 'category';

        Category::create($data);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('owner.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['icon'] = $data['icon'] ?? 'category';

        $category->update($data);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Prevent deletion if category has menus
        $menuCount = $category->menus()->count();
        if ($menuCount > 0) {
            return redirect()->route('owner.categories.index')
                ->with('error', "Tidak bisa menghapus \"{$category->name}\" karena masih memiliki {$menuCount} item menu.");
        }

        $category->delete();

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
