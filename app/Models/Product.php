<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const LABEL_OPTIONS = [
        'new' => 'Produk Terbaru',
        'best_seller' => 'Best Seller',
        'promo' => 'Promo',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'material',
        'fit_style',
        'weight_grams',
        'size_chart',
        'description',
        'labels',
        'status',
        'is_featured',
        'cost_price',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
        'labels' => 'array',
        'is_featured' => 'boolean',
        'cost_price' => 'decimal:2',
        'weight_grams' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first()?->path
            ?? $this->images()->first()?->path;
    }
}
