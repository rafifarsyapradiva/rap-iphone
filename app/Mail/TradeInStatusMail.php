<?php

namespace App\Mail;

use App\Models\TradeIn;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TradeInStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public TradeIn $tradeIn;

    public function __construct(TradeIn $tradeIn)
    {
        $this->tradeIn = $tradeIn->loadMissing(['user', 'targetOrder']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Status Trade-In #{$this->tradeIn->trade_in_code} ({$this->tradeIn->status_label}) - Rap-iPhone",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trade-ins.status',
            with: [
                'tradeIn' => $this->tradeIn,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
