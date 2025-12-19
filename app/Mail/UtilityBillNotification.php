<?php

namespace App\Mail;

use App\Models\UtilityBill;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UtilityBillNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;

    /**
     * Create a new message instance.
     */
    public function __construct(UtilityBill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo hóa đơn tiền phòng - ' . $this->bill->bill_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.utility-bill-notification',
            with: [
                'bill' => $this->bill,
                'viewUrl' => route('utility-bills.show', $this->bill->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
