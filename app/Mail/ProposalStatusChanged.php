<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Proposal;

class ProposalStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $proposal;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Proposal $proposal, $oldStatus, $newStatus)
    {
        $this->proposal = $proposal;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = match($this->newStatus) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_required' => 'Perlu Revisi',
            'under_review' => 'Sedang Review',
            default => 'Status Berubah'
        };

        return new Envelope(
            subject: 'Proposal ' . $statusText . ' - ' . $this->proposal->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.proposal-status-changed',
            text: 'emails.proposal-status-changed-text',
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
