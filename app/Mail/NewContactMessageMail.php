<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Hitting "reply" in the mail app answers the visitor directly.
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: 'New message from ' . $this->contact->name . ' — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact');
    }
}
