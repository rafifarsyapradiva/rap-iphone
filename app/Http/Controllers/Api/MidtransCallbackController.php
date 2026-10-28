<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\MidtransService;
use App\Services\NotificationOrchestrator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    protected MidtransService $midtransService;
    protected NotificationOrchestrator $notificationOrchestrator;

    public function __construct(
        MidtransService $midtransService,
        NotificationOrchestrator $notificationOrchestrator
    ) {
        $this->midtransService = $midtransService;
        $this->notificationOrchestrator = $notificationOrchestrator;
    }

    /**
     * Handle incoming Midtrans Webhook Callback HTTP POST.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = isset($payload['fraud_status']) ? (string) $payload['fraud_status'] : null;
        $transactionId = (string) ($payload['transaction_id'] ?? null);
        $paymentType = (string) ($payload['payment_type'] ?? null);
        $transactionTime = (string) ($payload['transaction_time'] ?? null);

        // Required parameters validation
        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signatureKey) || empty($transactionStatus)) {
            Log::warning('Midtrans Webhook: Missing required parameters in payload', $payload);
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter webhook tidak lengkap.',
            ], 422);
        }

        // 1. Verify SHA-512 Signature Hash
        if (!$this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::error("Midtrans Webhook: Invalid signature hash for Order: {$orderId}");
            return response()->json([
                'status' => 'error',
                'message' => 'Verifikasi signature hash gagal.',
            ], 403);
        }

        // 2. Locate Order by unique order_number
        $order = Order::where('order_number', $orderId)->with(['payment', 'items'])->first();

        if (!$order) {
            Log::error("Midtrans Webhook: Order not found: {$orderId}");
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // 3. Map status to Rap-iPhone domain status
        $targetStatus = $this->midtransService->mapTransactionStatus($transactionStatus, $fraudStatus);

        // 4. Idempotency Check: Avoid double-processing and duplicate stock decrement
        if ($order->status === 'paid' && $targetStatus === 'paid') {
            Log::info("Midtrans Webhook: Order {$orderId} is already paid. Skipping duplicate processing (Idempotent).");

            if ($order->payment) {
                $order->payment->update([
                    'raw_payload' => $payload,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan sudah berstatus lunas sebelumnya (Idempotent).',
            ], 200);
        }

        // 5. Database Transaction: Atomic state update and stock decrement
        $transitionedToPaid = false;

        DB::transaction(function () use (
            $order,
            $targetStatus,
            $transactionStatus,
            $transactionId,
            $paymentType,
            $transactionTime,
            $payload,
            &$transitionedToPaid
        ) {
            // Update or create payment record
            $parsedPaymentTime = $transactionTime ? Carbon::parse($transactionTime) : now();

            $order->payment()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'midtrans_transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'transaction_status' => $transactionStatus,
                    'payment_time' => $parsedPaymentTime,
                    'raw_payload' => $payload,
                ]
            );

            // If transaction successfully paid
            if ($targetStatus === 'paid' && $order->status !== 'paid') {
                $order->update(['status' => 'paid']);
                $transitionedToPaid = true;

                // Decrement inventory stock safely for each variant item
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)
                            ->decrement('stock', $item->quantity);
                    }
                }

                Log::info("Midtrans Webhook: Order {$order->order_number} marked as PAID. Inventory stock decremented.");
            } elseif ($targetStatus === 'cancelled' && $order->status !== 'cancelled') {
                $order->update(['status' => 'cancelled']);
                Log::info("Midtrans Webhook: Order {$order->order_number} marked as CANCELLED.");
            }
        });

        // 6. Trigger simultaneous WhatsApp & Email (+ PDF Invoice attachment) when newly paid
        if ($transitionedToPaid) {
            $order->refresh()->load(['user', 'items.variant.product', 'payment']);
            $this->notificationOrchestrator->notifyPaymentSuccess($order);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi Midtrans berhasil diproses.',
            'order_status' => $targetStatus,
        ], 200);
    }
}

