<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Hak unduh / cetak nota faktur PDF.
     *
     * - Admin & Owner: semua pesanan, semua status.
     * - Customer: hanya pesanan miliknya sendiri yang sudah lunas.
     */
    public function downloadInvoice(User $user, Order $order): bool
    {
        if ($user->hasAdminAccess()) {
            return true;
        }

        return (int) $order->user_id === (int) $user->id
            && in_array($order->status, config('invoice.downloadable_statuses', []), true);
    }
}

