<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\MidtransService;
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

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
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

        $clientKey = $this->midtransService->getClientKey();
        $isProduction = $this->midtransService->isProduction();

        return view('customer.checkout', compact('cart', 'subtotal', 'totalQuantity', 'user', 'couriers', 'clientKey', 'isProduction'));
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
        ]);

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
            $order = DB::transaction(function () use ($validated, $cart) {
                // Calculate totals
                $subtotal = 0;
                foreach ($cart as $item) {
                    $subtotal += ($item['price'] * $item['quantity']);
                }

                $shippingCost = 0.00; // Apple Store Free Shipping promo
                $discountAmount = 0.00;
                $grandTotal = $subtotal + $shippingCost - $discountAmount;

                // Unique Order Number: RAP-YYYYMMDD-XXXX
                $datePrefix = date('Ymd');
                $countToday = Order::whereDate('created_at', today())->count() + 1;
                $uniqueCode = str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
                $orderNumber = "RAP-{$datePrefix}-{$uniqueCode}";

                if (Order::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = "RAP-{$datePrefix}-" . strtoupper(Str::random(4));
                }

                // 1. Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => auth()->id(),
                    'total_items_price' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'status' => 'pending',
                    'shipping_courier' => $validated['shipping_courier'],
                    'shipping_address' => $validated['shipping_address'],
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'customer_notes' => $validated['customer_notes'] ?? null,
                ]);

                // 2. Create Order Items
                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $item['variant_id'],
                        'product_name' => $item['product_name'],
                        'variant_details' => $item['color_name'] . ' / ' . $item['storage_capacity'],
                        'price' => $item['price'],
                        'cost_price' => $item['cost_price'] ?? 0,
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'allocated_imei' => $item['imei'] ?? null,
                    ]);
                }

                // 3. Generate Snap Token via MidtransService
                $snapToken = $this->midtransService->getSnapToken($order);

                // 4. Create Payment Record
                Payment::create([
                    'order_id' => $order->id,
                    'gross_amount' => $grandTotal,
                    'snap_token' => $snapToken,
                    'transaction_status' => 'pending',
                ]);

                // 5. Clear Cart Session
                session()->forget('cart');
                session()->put('cart_count', 0);

                return $order;
            });

            $order->load(['payment', 'items']);

            $snapToken = $order->payment->snap_token;
            $redirectUrl = route('checkout.success', $order->order_number);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_number' => $order->order_number,
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'message' => 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.',
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
}

