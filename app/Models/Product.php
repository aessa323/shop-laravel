<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'name_en', 'slug', 'description', 'description_en',
        'price', 'sale_price', 'stock', 'sku', 'brand', 'model',
        'specs', 'thumbnail', 'category_id', 'is_active', 'is_featured',
        'is_best_seller', 'is_flash_sale', 'flash_sale_ends_at',
    ];

    protected $casts = [
        'specs'            => 'array',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'is_best_seller'   => 'boolean',
        'is_flash_sale'    => 'boolean',
        'flash_sale_ends_at' => 'datetime',
        'price'            => 'decimal:2',
        'sale_price'       => 'decimal:2',
    ];

    // ========== العلاقات ==========

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ========== Accessors ==========

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->sale_price && $this->price > 0) {
            return (int)(($this->price - $this->sale_price) / $this->price * 100);
        }
        return null;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)
                     ->where('flash_sale_ends_at', '>', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
 