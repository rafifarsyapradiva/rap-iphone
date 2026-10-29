<?php

namespace App\Policies;

use App\Models\TradeIn;
use App\Models\User;

class TradeInPolicy
{
    /**
     * Hak melihat data taksiran dan pengajuan Trade-In (IDOR Mitigation).
     *
     * - Admin & Owner: semua pengajuan trade-in (untuk keperluan inspeksi dan review).
     * - Customer: hanya pengajuan miliknya sendiri.
     */
    public function view(User $user, TradeIn $tradeIn): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }

        return (int) $tradeIn->user_id === (int) $user->id;
    }

    /**
     * Hak mengupdate/menginspeksi pengajuan Trade-In (Admin/Owner only).
     */
    public function update(User $user, TradeIn $tradeIn): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Hak membatalkan pengajuan Trade-In.
     *
     * - Admin/Owner: dapat membatalkan semua yang masih berstatus submitted.
     * - Customer: hanya milik sendiri yang masih berstatus submitted.
     */
    public function cancel(User $user, TradeIn $tradeIn): bool
    {
        if ($user->hasAdminAccess()) {
            return in_array($tradeIn->status, ['submitted', 'unit_received'], true);
        }

        return (int) $tradeIn->user_id === (int) $user->id && $tradeIn->status === 'submitted';
    }
}

