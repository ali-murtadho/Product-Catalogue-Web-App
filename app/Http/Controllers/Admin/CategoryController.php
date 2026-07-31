<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = Category::ordered()->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = [
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = [
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Proteksi delete: cek apakah kategori memiliki produk terkait
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait. Pindahkan atau hapus produk terlebih dahulu.');
        }

        // Delete image from storage if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Update the sort order of categories.
     * Accepts an array of category IDs with their new sort_order values.
     */
    public function updateOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer', 'exists:categories,id'],
            'order.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['order'] as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Urutan kategori berhasil diperbarui.');
    }
}
