<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeIn extends Model
{
    use HasFactory;

    public const VOUCHER_BANK_MARKER = 'Kupon Potongan Belanja';

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

    /**
     * Check if the customer selected Store Discount Voucher as payout method.
     */
    public function isVoucherPayout(): bool
    {
        return $this->customer_bank_name === self::VOUCHER_BANK_MARKER
            || str_starts_with((string) $this->customer_account_number, 'VCH-');
    }

    /**
     * Get human-readable Indonesian label for current status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'Menunggu Penyerahan Unit',
            'unit_received' => 'Unit Diterima di Toko',
            'inspected' => 'Selesai Diinspeksi Teknisi',
            'approved' => 'Penawaran Disetujui',
            'rejected' => 'Pengajuan Ditolak',
            'paid_out' => $this->isVoucherPayout() ? 'Kupon Potongan Aktif' : 'Dana Telah Ditransfer',
            default => strtoupper((string) $this->status),
        };
    }

    /**
     * Get CSS badge class for current status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'badge-pending',
            'unit_received', 'inspected' => 'badge-processing',
            'approved', 'paid_out' => 'badge-paid',
            'rejected' => 'badge-cancelled',
            default => 'badge-pending',
        };
    }

    /**
     * Get the effective deal price (final_price if set, otherwise estimated_price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->final_price ?? $this->estimated_price);
    }
}
