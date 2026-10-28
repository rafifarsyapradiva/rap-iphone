<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_in_code',
        'user_id',
        'target_order_id',
        'phone_model',
        'storage_capacity',
        'battery_health',
        'physical_condition',
        'screen_condition',
        'accessories_status',
        'imei',
        'estimated_price',
        'final_price',
        'status',
        'customer_bank_name',
        'customer_account_number',
        'customer_account_holder',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'battery_health' => 'integer',
            'estimated_price' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user who submitted the trade-in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order linked to this trade-in (if traded in for new iPhone).
     */
    public function targetOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'target_order_id');
    }
}
