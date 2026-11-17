<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user, public $settings, public $template, public string $resetCode = ''
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $template = $this->template;

        return new Envelope(
            subject: $template->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // The stored code is only the hash; expose the plaintext so the emailed link works
        // while the database keeps nothing replayable. Not persisted (no save()).
        if ($this->resetCode !== '') {
            $this->user->password_reset_code = $this->resetCode;
        }

        return new Content(
            view: 'mail.reset',
            with: ['resetCode' => $this->resetCode],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
