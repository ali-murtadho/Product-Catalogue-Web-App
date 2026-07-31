<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock_quantity',
        'is_unlimited',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_unlimited' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $model) {
            $model->slug = static::generateUniqueSlug($model->name);
        });

        static::updating(function (Product $model) {
            if ($model->isDirty('name')) {
                $model->slug = static::generateUniqueSlug($model->name, $model->id);
            }
        });
    }

    /**
     * Generate a unique slug from the given name.
     */
    protected static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Scope to get featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get in-stock products (stock > 0 or unlimited).
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('stock_quantity', '>', 0)
              ->orWhere('is_unlimited', true);
        });
    }

    /**
     * Scope to get low-stock products (stock <= 2 and not unlimited).
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '<=', 2)
                     ->where('is_unlimited', false);
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image for this product.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the variants for this product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
