<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'storage_capacity',
        'color_name',
        'color_hex',
        'cost_price',
        'selling_price',
        'stock',
        'sku',
        'battery_health',
        'imei',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'battery_health' => 'integer',
        ];
    }

    /**
     * Get the product that owns this variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get images specific to this variant.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    /**
     * Get order items associated with this variant.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /**
     * Check if variant has available stock.
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Gross profit margin per unit (for Owner analytics).
     */
    protected function margin(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->selling_price - $this->cost_price),
        );
    }

    protected function profitMargin(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->selling_price - $this->cost_price),
        );
    }
}
