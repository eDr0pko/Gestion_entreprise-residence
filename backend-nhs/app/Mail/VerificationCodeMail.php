<?php

    namespace App\Mail;

    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Mail\Mailables\Envelope;
    use Illuminate\Queue\SerializesModels;

    class VerificationCodeMail extends Mailable
    {
        use Queueable, SerializesModels;

        public $code;
        public $userName;

        /**
         * Create a new message instance.
         */
        public function __construct($code, $userName = null)
        {
            $this->code = $code;
            $this->userName = $userName;
        }

        /**
         * Get the message envelope.
         */
        public function envelope(): Envelope
        {
            return new Envelope(
                subject: 'Code de verification - Gestion Residence',
            );
        }

        /**
         * Get the message content definition.
         */
        public function content(): Content
        {
            return new Content(
                html: 'emails.verification-code',
                text: 'emails.verification-code-text',
                with: [
                    'verificationCode' => $this->code,
                    'userName' => $this->userName,
                    'code' => $this->code // Pour le template texte
                ]
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
?>


