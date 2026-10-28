<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoicePdfService;
use Illuminate\Http\Response;

class InvoiceVerificationController extends Controller
{
    public function __construct(protected InvoicePdfService $invoicePdfService)
    {
    }

    /**
     * Halaman publik verifikasi keaslian nota (target QR Code pada nota PDF).
     * Hanya menampilkan data non-sensitif yang sudah disamarkan.
     */
    public function show(string $orderNumber, string $code): Response
    {
        $order = Order::with(['items.variant.product', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();

        $isValid = $order !== null && $this->invoicePdfService->isValidVerificationCode($order, $code);

        if (! $isValid) {
            return response()->view('invoice.verify', [
                'isValid' => false,
                'orderNumber' => $orderNumber,
            ], 404);
        }

        $items = $order->items->map(fn ($item) => [
            'name' => $item->product_name,
            'variant' => $item->variant_details,
            'quantity' => $item->quantity,
            'imei' => $this->invoicePdfService->maskImei($item->allocated_imei ?: $item->variant?->imei),
            'warranty' => $this->invoicePdfService->warrantyFor($item, $order),
        ]);

        return response()->view('invoice.verify', [
            'isValid' => true,
            'order' => $order,
            'orderNumber' => $order->order_number,
            'status' => $this->invoicePdfService->statusMeta($order),
            'maskedName' => $this->invoicePdfService->maskName($order->recipient_name),
            'items' => $items,
            'store' => config('invoice.store'),
        ]);
    }
}

