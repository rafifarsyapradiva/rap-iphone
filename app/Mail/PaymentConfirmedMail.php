<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\InvoicePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['user', 'items.variant.product', 'payment']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pembayaran Terverifikasi & Nota Resmi #{$this->order->order_number} - Rap-iPhone",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.payment-confirmed',
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Automatically attach the generated PDF Invoice to the payment confirmation email.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        /** @var InvoicePdfService $pdfService */
        $pdfService = app(InvoicePdfService::class);
        $filename = $pdfService->getFilename($this->order);

        return [
            Attachment::fromData(
                fn () => $pdfService->generatePdfBinary($this->order),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
