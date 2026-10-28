<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;

class InvoicePdfService
{
    /**
     * Generate DomPDF instance for the specified Order.
     */
    public function generatePdf(Order $order): DomPdfInstance
    {
        $order->loadMissing(['user', 'items.variant.product', 'payment', 'tradeIn']);

        $verificationHash = strtoupper(substr(hash('sha256', $order->order_number . '|' . $order->grand_total . '|' . config('app.key')), 0, 16));

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'verificationHash' => $verificationHash,
            'generatedAt' => now()->format('d M Y, H:i') . ' WIB',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Render raw PDF binary string for email attachments.
     */
    public function generatePdfBinary(Order $order): string
    {
        return $this->generatePdf($order)->output();
    }

    /**
     * Get standardized PDF filename for an Order.
     */
    public function getFilename(Order $order): string
    {
        return 'Nota-Rap-iPhone-' . $order->order_number . '.pdf';
    }
}
