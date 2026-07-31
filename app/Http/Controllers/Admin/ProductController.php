<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = Product::with(['category', 'primaryImage'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::ordered()->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_unlimited' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.variant_name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.variant_value' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.price_impact' => ['nullable', 'numeric'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_unlimited' => $validated['is_unlimited'] ?? false,
                'is_featured' => $validated['is_featured'] ?? false,
            ]);

            // Handle multi-image upload
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $product);
            }

            // Handle variants
            if (!empty($validated['variants'])) {
                $this->handleVariants($validated['variants'], $product);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load(['images', 'variants']);
        $categories = Category::ordered()->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_unlimited' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.variant_name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.variant_value' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.price_impact' => ['nullable', 'numeric'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_unlimited' => $validated['is_unlimited'] ?? false,
                'is_featured' => $validated['is_featured'] ?? false,
            ]);

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $existingCount = $product->images()->count();
                $newImages = $request->file('images');
                $totalAllowed = 5 - $existingCount;

                if (count($newImages) > $totalAllowed) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Maksimal 5 gambar per produk. Saat ini sudah ada {$existingCount} gambar.");
                }

                $isPrimary = $product->images()->where('is_primary', true)->exists() ? false : true;
                $this->handleImageUpload($newImages, $product, $isPrimary);
            }

            // Handle deleted images
            if ($request->has('delete_images')) {
                $this->deleteImages($request->input('delete_images'), $product);
            }

            // Handle variants update
            $this->syncVariants($validated['variants'] ?? [], $product);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage (cascade delete).
     */
    public function destroy(Product $product): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Delete all images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete images from DB
            $product->images()->delete();

            // Delete variants from DB
            $product->variants()->delete();

            // Delete the product
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.products.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Handle multi-image upload with compression.
     *
     * @param array $files
     * @param Product $product
     * @param bool $firstIsPrimary
     */
    private function handleImageUpload(array $files, Product $product, bool $firstIsPrimary = true): void
    {
        $sortOrder = $product->images()->max('sort_order') ?? 0;

        foreach ($files as $index => $file) {
            $sortOrder++;
            $filename = uniqid('product_') . '.' . $file->getClientOriginalExtension();
            $path = 'products/' . $filename;

            // Compress and save image
            $this->compressAndSaveImage($file, $path);

            $isPrimary = $firstIsPrimary && $index === 0;

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * Compress image: resize to max 1200px width, quality 80%.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Relative path within public disk
     */
    private function compressAndSaveImage($file, string $path): void
    {
        $mime = $file->getMimeType();
        $absolutePath = Storage::disk('public')->path($path);

        // Ensure directory exists
        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Create image resource based on mime type
        $sourceImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($file->getPathname()),
            'image/png' => imagecreatefrompng($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default => imagecreatefromjpeg($file->getPathname()),
        };

        if (!$sourceImage) {
            // Fallback: store without compression
            $file->storeAs('products', basename($path), 'public');
            return;
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        $maxWidth = 1200;

        // Resize if wider than max width
        if ($originalWidth > $maxWidth) {
            $ratio = $maxWidth / $originalWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) round($originalHeight * $ratio);

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and WebP
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            imagedestroy($sourceImage);
            $sourceImage = $resizedImage;
        }

        // Save compressed image
        match ($mime) {
            'image/jpeg' => imagejpeg($sourceImage, $absolutePath, 80),
            'image/png' => imagepng($sourceImage, $absolutePath, 8), // PNG compression 0-9, 8 ≈ 80% quality
            'image/webp' => imagewebp($sourceImage, $absolutePath, 80),
            default => imagejpeg($sourceImage, $absolutePath, 80),
        };

        imagedestroy($sourceImage);
    }

    /**
     * Handle creating variants for a product.
     */
    private function handleVariants(array $variants, Product $product): void
    {
        foreach ($variants as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => $variant['variant_name'],
                'variant_value' => $variant['variant_value'],
                'price_impact' => $variant['price_impact'] ?? 0,
                'stock_quantity' => $variant['stock_quantity'] ?? 0,
            ]);
        }
    }

    /**
     * Sync variants: update existing, create new, delete removed.
     */
    private function syncVariants(array $variants, Product $product): void
    {
        $existingIds = $product->variants()->pluck('id')->toArray();
        $updatedIds = [];

        foreach ($variants as $variant) {
            if (!empty($variant['id']) && in_array($variant['id'], $existingIds)) {
                // Update existing variant
                ProductVariant::where('id', $variant['id'])->update([
                    'variant_name' => $variant['variant_name'],
                    'variant_value' => $variant['variant_value'],
                    'price_impact' => $variant['price_impact'] ?? 0,
                    'stock_quantity' => $variant['stock_quantity'] ?? 0,
                ]);
                $updatedIds[] = $variant['id'];
            } else {
                // Create new variant
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['variant_name'],
                    'variant_value' => $variant['variant_value'],
                    'price_impact' => $variant['price_impact'] ?? 0,
                    'stock_quantity' => $variant['stock_quantity'] ?? 0,
                ]);
            }
        }

        // Delete variants that were removed
        $toDelete = array_diff($existingIds, $updatedIds);
        if (!empty($toDelete)) {
            ProductVariant::whereIn('id', $toDelete)->delete();
        }
    }

    /**
     * Delete specific images from a product.
     */
    private function deleteImages(array $imageIds, Product $product): void
    {
        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // If primary was deleted, set first remaining image as primary
        if (!$product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->orderBy('sort_order')->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }
}
