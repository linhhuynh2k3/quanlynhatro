<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contract $contract;

    /**
     * Create a new message instance.
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract->loadMissing('listing', 'landlord', 'tenant');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận ký hợp đồng #' . $this->contract->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contracts.signed',
            with: [
                'contract' => $this->contract,
            ],
        );
    }
}

