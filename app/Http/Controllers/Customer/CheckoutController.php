<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Services\MidtransService;
use App\Services\NotificationOrchestrator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
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
     * Display the distraction-free checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih unit iPhone terlebih dahulu.');
        }

        $subtotal = 0;
        $totalQuantity = 0;

        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalQuantity += $item['quantity'];
        }

        $user = auth()->user();

        $couriers = [
            [
                'id' => 'JNE Express',
                'name' => 'JNE Express (Reguler / YES)',
                'eta' => '1 - 2 Hari Kerja',
                'cost' => 0,
                'badge' => 'Promo Bebas Ongkir',
            ],
            [
                'id' => 'SiCepat Ekspres',
                'name' => 'SiCepat Ekspres (BEST)',
                'eta' => '1 - 2 Hari Kerja',
                'cost' => 0,
                'badge' => 'Promo Bebas Ongkir',
            ],
            [
                'id' => 'J&T Express',
                'name' => 'J&T Express Super',
                'eta' => '2 - 3 Hari Kerja',
                'cost' => 0,
                'badge' => 'Promo Bebas Ongkir',
            ],
            [
                'id' => 'Pick-up Store',
                'name' => 'Ambil Sendiri di Store Resmi Rap-iPhone',
                'eta' => 'Siap dalam 15 Menit',
                'cost' => 0,
                'badge' => 'Instan',
            ],
        ];

        $availableVouchers = TradeIn::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'paid_out'])
            ->where(function ($q) {
                $q->where('customer_bank_name', TradeIn::VOUCHER_BANK_MARKER)
                  ->orWhere('customer_account_number', 'like', 'VCH-%');
            })
            ->whereNull('target_order_id')
            ->get();

        $clientKey = $this->midtransService->getClientKey();
        $isProduction = $this->midtransService->isProduction();

        return view('customer.checkout', compact('cart', 'subtotal', 'totalQuantity', 'user', 'couriers', 'clientKey', 'isProduction', 'availableVouchers'));
    }

    /**
     * Process checkout submission, generate Order and Midtrans Snap Token.
     */
    public function process(Request $request): JsonResponse|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang belanja Anda kosong.',
                ], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:150',
            'recipient_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string|max:1000',
            'shipping_courier' => 'required|string|max:50',
            'customer_notes' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string|max:50',
        ]);

        // Extract voucher code from dedicated input or customer_notes
        $voucherCode = trim((string) ($request->input('voucher_code') ?? ''));
        if ($voucherCode === '' && !empty($validated['customer_notes'])) {
            if (preg_match('/(VCH-[A-Z0-9]{4,10})/i', $validated['customer_notes'], $matches)) {
                $voucherCode = strtoupper($matches[1]);
            }
        }

        $appliedTradeIn = null;
        if ($voucherCode !== '') {
            $appliedTradeIn = TradeIn::where('customer_account_number', strtoupper($voucherCode))
                ->whereIn('status', ['approved', 'paid_out'])
                ->whereNull('target_order_id')
                ->where('user_id', auth()->id())
                ->first();
        }

        // Stock availability verification
        foreach ($cart as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || $variant->stock < $item['quantity']) {
                $err = "Maaf, stok unit {$item['product_name']} tidak mencukupi (Tersisa " . ($variant ? $variant->stock : 0) . " unit).";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $err], 422);
                }
                return back()->with('error', $err);
            }
        }

        try {
            $order = DB::transaction(function () use ($validated, $cart, $appliedTradeIn) {
                // Calculate totals
                $subtotal = 0;
                foreach ($cart as $item) {
                    $subtotal += ($item['price'] * $item['quantity']);
                }

                $shippingCost = 0.00; // Apple Store Free Shipping promo
                $discountAmount = 0.00;

                if ($appliedTradeIn) {
                    $voucherVal = (float) ($appliedTradeIn->final_price ?? $appliedTradeIn->estimated_price);
                    $discountAmount = min($subtotal, $voucherVal);
                }

                $grandTotal = max(0.00, $subtotal + $shippingCost - $discountAmount);

                // Unique Order Number: RAP-YYYYMMDD-XXXX
                $datePrefix = date('Ymd');
                $countToday = Order::whereDate('created_at', today())->count() + 1;
                $uniqueCode = str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
                $orderNumber = "RAP-{$datePrefix}-{$uniqueCode}";

                if (Order::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = "RAP-{$datePrefix}-" . strtoupper(Str::random(4));
                }

                $initialStatus = ($grandTotal <= 0.00) ? 'paid' : 'pending';

                // 1. Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => auth()->id(),
                    'total_items_price' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'status' => $initialStatus,
                    'shipping_courier' => $validated['shipping_courier'],
                    'shipping_address' => $validated['shipping_address'],
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'customer_notes' => $validated['customer_notes'] ?? null,
                ]);

                // Link trade-in voucher if applied
                if ($appliedTradeIn) {
                    $appliedTradeIn->update([
                        'target_order_id' => $order->id,
                        'status' => 'paid_out',
                    ]);
                }

                // 2. Create Order Items
                foreach ($cart as $item) {
                    $variant = ProductVariant::find($item['variant_id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $item['variant_id'],
                        'product_name' => $item['product_name'],
                        'variant_details' => $item['color_name'] . ' / ' . $item['storage_capacity'],
                        'price' => $item['price'],
                        'cost_price' => $item['cost_price'] ?? ($variant ? (float) $variant->cost_price : 0),
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'allocated_imei' => $item['imei'] ?? ($variant ? $variant->imei : null),
                    ]);
                }

                // 3. Payment record creation
                if ($grandTotal <= 0.00) {
                    // Fully covered by Trade-In Voucher
                    Payment::create([
                        'order_id' => $order->id,
                        'gross_amount' => 0.00,
                        'payment_type' => 'voucher',
                        'snap_token' => 'voucher_full_' . md5($order->order_number),
                        'transaction_status' => 'settlement',
                        'payment_time' => now(),
                    ]);

                    // Immediate stock decrement with row lock
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $variantRow = ProductVariant::where('id', $item->product_variant_id)
                                ->lockForUpdate()
                                ->first();
                            if ($variantRow) {
                                $newStock = max(0, (int) $variantRow->stock - (int) $item->quantity);
                                $variantRow->update(['stock' => $newStock]);
                            }
                        }
                    }
                } else {
                    // Generate Snap Token via MidtransService
                    $snapToken = $this->midtransService->getSnapToken($order);

                    Payment::create([
                        'order_id' => $order->id,
                        'gross_amount' => $grandTotal,
                        'snap_token' => $snapToken,
                        'transaction_status' => 'pending',
                    ]);
                }

                // 4. Clear Cart Session
                session()->forget('cart');
                session()->put('cart_count', 0);

                return $order;
            });

            $order->load(['payment', 'items', 'user']);

            // Trigger notification based on order status
            if ($order->status === 'paid') {
                $this->notificationOrchestrator->notifyPaymentSuccess($order);
            } else {
                $this->notificationOrchestrator->notifyOrderCreated($order);
            }

            $snapToken = $order->payment?->snap_token;
            $redirectUrl = route('checkout.success', $order->order_number);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_number' => $order->order_number,
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'message' => ($order->status === 'paid')
                        ? 'Pesanan berhasil dibayar menggunakan Kupon Voucher Trade-In!'
                        : 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.',
                ]);
            }

            return redirect()->route('checkout.success', $order->order_number);

        } catch (Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses pesanan: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan sistem saat memproses pesanan. Silakan coba kembali.');
        }
    }

    /**
     * Display post-checkout success / status review page.
     */
    public function success(Order $order): View
    {
        abort_unless(
            $order->user_id === auth()->id() || (auth()->check() && auth()->user()->hasRole('admin', 'owner')),
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );

        $order->load(['items.variant', 'payment']);

        $clientKey = $this->midtransService->getClientKey();
        $isProduction = $this->midtransService->isProduction();

        return view('customer.checkout-success', compact('order', 'clientKey', 'isProduction'));
    }

    /**
     * Simulate Midtrans Sandbox Payment (QRIS & Virtual Account) by dispatching a signed webhook payload.
     */
    public function simulateSandboxPayment(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        abort_unless(
            $order->user_id === auth()->id() || (auth()->check() && auth()->user()->hasRole('admin', 'owner')),
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );

        abort_if(
            $this->midtransService->isProduction(),
            403,
            'Simulasi pembayaran hanya dapat dijalankan pada mode Sandbox Midtrans.'
        );

        $validated = $request->validate([
            'payment_method' => 'required|in:qris,bca_va,mandiri_va,bni_va,bri_va',
            'simulation_status' => 'nullable|in:settlement,pending,expire,cancel',
        ]);

        $method = $validated['payment_method'];
        $transactionStatus = $validated['simulation_status'] ?? 'settlement';

        $statusCode = match ($transactionStatus) {
            'settlement' => '200',
            'pending' => '201',
            default => '202',
        };

        $grossAmount = number_format((float) $order->grand_total, 2, '.', '');
        $serverKey = $this->midtransService->getServerKey();
        $signatureKey = hash('sha512', $order->order_number . $statusCode . $grossAmount . $serverKey);

        $isQris = $method === 'qris';
        $paymentType = $isQris ? 'qris' : 'bank_transfer';
        $bankCode = $isQris ? null : str_replace('_va', '', $method);

        $webhookPayload = [
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => $transactionStatus,
            'fraud_status' => 'accept',
            'transaction_id' => 'mid-sandbox-' . $method . '-' . strtolower(Str::random(8)),
            'payment_type' => $paymentType,
            'transaction_time' => now()->format('Y-m-d H:i:s'),
            'sandbox_simulated' => true,
            'selected_channel' => strtoupper($method),
        ];

        if ($isQris) {
            $webhookPayload['issuer'] = 'gopay';
            $webhookPayload['acquirer'] = 'gopay';
        } else {
            $vaPrefix = match ($bankCode) {
                'bca' => '80777',
                'mandiri' => '88908',
                'bni' => '88100',
                'bri' => '88888',
                default => '80000',
            };
            $webhookPayload['va_numbers'] = [
                [
                    'bank' => $bankCode,
                    'va_number' => $vaPrefix . preg_replace('/\D+/', '', $order->recipient_phone ?: '081234567890'),
                ],
            ];
        }

        $callbackResponse = app(\App\Http\Controllers\Api\MidtransCallbackController::class)
            ->handle(new Request($webhookPayload));

        $order->refresh();

        $channelLabel = match ($method) {
            'qris' => 'QRIS Midtrans (GoPay / ShopeePay / Mobile Banking)',
            'bca_va' => 'BCA Virtual Account',
            'mandiri_va' => 'Mandiri Bill / Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            default => strtoupper($method),
        };

        $message = match ($order->status) {
            'paid' => "Simulasi pembayaran Sandbox via {$channelLabel} BERHASIL! Status pesanan #{$order->order_number} otomatis diperbarui menjadi LUNAS (PAID).",
            'cancelled' => "Simulasi pembatalan/kadaluarsa Sandbox selesai. Pesanan #{$order->order_number} berstatus DIBATALKAN.",
            default => "Simulasi instruksi pembayaran {$channelLabel} dicatat (Status: MENUNGGU PEMBAYARAN).",
        };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $callbackResponse->getStatusCode() === 200,
                'order_number' => $order->order_number,
                'order_status' => $order->status,
                'payment_type' => $paymentType,
                'selected_channel' => $channelLabel,
                'message' => $message,
            ], $callbackResponse->getStatusCode());
        }

        return redirect()
            ->route('checkout.success', $order->order_number)
            ->with('success', $message);
    }
}

