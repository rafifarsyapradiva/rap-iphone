<?php

namespace App\Services;

use App\Mail\OrderInvoiceMail;
use App\Mail\OrderShippedMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\TradeInStatusMail;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\TradeIn;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Testing\Fakes\MailFake;
use Throwable;

class NotificationOrchestrator
{
    protected FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Trigger simultaneous WhatsApp & Email notifications when a new Order is created.
     *
     * @return array{whatsapp: NotificationLog, email: NotificationLog}
     */
    public function notifyOrderCreated(Order $order): array
    {
        $order->loadMissing(['user', 'items.variant.product', 'payment']);

        $phone = (string) ($order->recipient_phone ?: ($order->user->phone_number ?? ''));
        $email = (string) ($order->user->email ?? '');

        $waMessage = $this->fonnteService->buildOrderCreatedMessage($order);
        $whatsappLog = $this->dispatchWhatsApp($phone, $waMessage, 'order_created');

        $emailLog = $this->dispatchEmail(
            $email,
            new OrderInvoiceMail($order),
            'order_created',
            [
                'order_number' => $order->order_number,
                'mailable' => OrderInvoiceMail::class,
                'has_pdf_attachment' => false,
            ]
        );

        return [
            'whatsapp' => $whatsappLog,
            'email' => $emailLog,
        ];
    }

    /**
     * Trigger simultaneous WhatsApp & Email (with PDF Invoice attachment) when payment is verified as PAID.
     *
     * @return array{whatsapp: NotificationLog, email: NotificationLog}
     */
    public function notifyPaymentSuccess(Order $order): array
    {
        $order->loadMissing(['user', 'items.variant.product', 'payment']);

        $phone = (string) ($order->recipient_phone ?: ($order->user->phone_number ?? ''));
        $email = (string) ($order->user->email ?? '');

        $waMessage = $this->fonnteService->buildPaymentSuccessMessage($order);
        $whatsappLog = $this->dispatchWhatsApp($phone, $waMessage, 'payment_success');

        $emailLog = $this->dispatchEmail(
            $email,
            new PaymentConfirmedMail($order),
            'payment_success',
            [
                'order_number' => $order->order_number,
                'mailable' => PaymentConfirmedMail::class,
                'has_pdf_attachment' => true,
                'attachment_filename' => 'Nota-Rap-iPhone-' . $order->order_number . '.pdf',
            ]
        );

        return [
            'whatsapp' => $whatsappLog,
            'email' => $emailLog,
        ];
    }

    /**
     * Trigger simultaneous WhatsApp & Email notifications when an Order is shipped with a tracking number.
     *
     * @return array{whatsapp: NotificationLog, email: NotificationLog}
     */
    public function notifyOrderShipped(Order $order): array
    {
        $order->loadMissing(['user', 'items.variant.product', 'payment']);

        $phone = (string) ($order->recipient_phone ?: ($order->user->phone_number ?? ''));
        $email = (string) ($order->user->email ?? '');

        $waMessage = $this->fonnteService->buildOrderShippedMessage($order);
        $whatsappLog = $this->dispatchWhatsApp($phone, $waMessage, 'order_shipped');

        $emailLog = $this->dispatchEmail(
            $email,
            new OrderShippedMail($order),
            'order_shipped',
            [
                'order_number' => $order->order_number,
                'tracking_number' => $order->shipping_tracking_number,
                'courier' => $order->shipping_courier,
                'mailable' => OrderShippedMail::class,
            ]
        );

        return [
            'whatsapp' => $whatsappLog,
            'email' => $emailLog,
        ];
    }

    /**
     * Trigger simultaneous WhatsApp & Email notifications for Trade-In submission or status updates.
     *
     * @return array{whatsapp: NotificationLog, email: NotificationLog}
     */
    public function notifyTradeInStatus(TradeIn $tradeIn, string $eventName = 'trade_in_status'): array
    {
        $tradeIn->loadMissing(['user', 'targetOrder']);

        $phone = (string) ($tradeIn->user->phone_number ?? '');
        $email = (string) ($tradeIn->user->email ?? '');

        $waMessage = $this->fonnteService->buildTradeInStatusMessage($tradeIn);
        $whatsappLog = $this->dispatchWhatsApp($phone, $waMessage, $eventName);

        $emailLog = $this->dispatchEmail(
            $email,
            new TradeInStatusMail($tradeIn),
            $eventName,
            [
                'trade_in_code' => $tradeIn->trade_in_code,
                'trade_in_status' => $tradeIn->status,
                'mailable' => TradeInStatusMail::class,
            ]
        );

        return [
            'whatsapp' => $whatsappLog,
            'email' => $emailLog,
        ];
    }

    /**
     * Dispatch WhatsApp message via FonnteService and record outcome to notifications_log.
     */
    protected function dispatchWhatsApp(string $phone, string $message, string $eventName): NotificationLog
    {
        $result = $this->fonnteService->sendMessage($phone, $message);

        $recipient = $result['recipient'] !== '' ? $result['recipient'] : ($phone !== '' ? $phone : '-');

        return NotificationLog::create([
            'channel' => 'whatsapp',
            'recipient' => $recipient,
            'event_name' => $eventName,
            'status' => $result['success'] ? 'sent' : 'failed',
            'error_message' => $result['error'],
            'response_payload' => $result['response'],
        ]);
    }

    /**
     * Dispatch Mailable via Laravel Mail and record outcome to notifications_log.
     *
     * @param array<string, mixed> $meta
     */
    protected function dispatchEmail(string $email, Mailable $mailable, string $eventName, array $meta = []): NotificationLog
    {
        $trimmedEmail = trim($email);

        if ($trimmedEmail === '') {
            return NotificationLog::create([
                'channel' => 'email',
                'recipient' => '-',
                'event_name' => $eventName,
                'status' => 'failed',
                'error_message' => 'Alamat email penerima kosong atau tidak tersedia.',
                'response_payload' => $meta,
            ]);
        }

        try {
            $isMailFake = Mail::getFacadeRoot() instanceof MailFake;
            $defaultMailer = (string) config('mail.default', 'log');
            $smtpUsername = (string) config('mail.mailers.smtp.username', '');

            if (!$isMailFake && $defaultMailer === 'smtp' && $smtpUsername === '') {
                Mail::mailer('log')->to($trimmedEmail)->send($mailable);
                $meta['mailer_driver'] = 'log_fallback';
            } else {
                Mail::to($trimmedEmail)->send($mailable);
                $meta['mailer_driver'] = $defaultMailer;
            }

            return NotificationLog::create([
                'channel' => 'email',
                'recipient' => $trimmedEmail,
                'event_name' => $eventName,
                'status' => 'sent',
                'error_message' => null,
                'response_payload' => $meta,
            ]);
        } catch (Throwable $e) {
            Log::error("NotificationOrchestrator Email Error ({$trimmedEmail} / {$eventName}): " . $e->getMessage());

            return NotificationLog::create([
                'channel' => 'email',
                'recipient' => $trimmedEmail,
                'event_name' => $eventName,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'response_payload' => $meta,
            ]);
        }
    }
}
