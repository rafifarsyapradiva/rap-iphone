<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function __construct(protected InvoicePdfService $invoicePdfService)
    {
    }

    /**
     * Unduh nota faktur PDF (Content-Disposition: attachment).
     */
    public function download(Order $order): Response
    {
        Gate::authorize('downloadInvoice', $order);

        return $this->invoicePdfService
            ->generatePdf($order)
            ->download($this->invoicePdfService->getFilename($order));
    }

    /**
     * Tampilkan nota faktur PDF inline di browser (siap dicetak).
     */
    public function preview(Order $order): Response
    {
        Gate::authorize('downloadInvoice', $order);

        return $this->invoicePdfService
            ->generatePdf($order)
            ->stream($this->invoicePdfService->getFilename($order));
    }
}

