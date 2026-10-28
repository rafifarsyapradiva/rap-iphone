<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_items_price',
        'shipping_cost',
        'discount_amount',
        'grand_total',
        'status',
        'shipping_courier',
        'shipping_tracking_number',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'customer_notes',
    ];

    protected function casts(): array
    {
        return [
            'total_items_price' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * Get the user who placed this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the line items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment transaction for this order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the trade-in associated with this order (if used as discount).
     */
    public function tradeIn(): HasOne
    {
        return $this->hasOne(TradeIn::class, 'target_order_id');
    }
}
