<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Get user name
     * 
     * @var string
     */
    protected $name;

    /**
     * Get user mail
     * 
     * @var string
     */
    protected $mail;

    /**
     * Get user password
     * 
     * @var string
     */
    protected $password;

     /**
     * Get user password
     * 
     * @var object
     */
    protected $logement;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $name, string $mail, string $password, object $logement)
    {
        // Get this user informations
        $this->name = $name;
        $this->mail = $mail;
        $this->password = $password;

        // Get lodging informations
        $this->logement = $logement;

        config(['mail.mailers.smtp.host' => 'smtp.gmail.com']);
        config(['mail.mailers.smtp.port' => '587']);
        config(['mail.mailers.smtp.username' => $name]);
        config(['mail.mailers.smtp.password' => $password]);

        // // Clear APP Cache
        // Artisan::call('optimize');
        // Artisan::call('config:clear');
        // Artisan::call('route:clear');
        
        // Update .env constants values
        // $_ENV['MAIL_HOST'] = 'smtp.gmail.com';
        // $_ENV['MAIL_USERNAME'] = $mail;
        // $_ENV['MAIL_PASSWORD'] = $password;
        // $_ENV['MAIL_FROM_NAME'] = $name;
        // $_ENV['MAIL_PORT'] = '587';
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address($this->mail, $this->name),
            replyTo: [
                new Address($this->mail, $this->name),
            ],
            subject: $this->logement->title . ': Demande de réservation',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mails.booking',
            with: [
                'logement' => $this->logement,
                'author'   => $this->name
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
