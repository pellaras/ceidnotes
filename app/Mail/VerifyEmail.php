<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $username;
    public $name;
    public $url;

    public function __construct($username, $name)
    {
        $this->username = $username;
        $this->name = $name;
    }

    public function build()
    {
        $this->url = $this->verificationUrl();

        return $this->markdown('mail.verify-email')
            ->subject('Ολοκλήρωση Εγγραφής στο ceidnotes.net')
            ->to($this->username . '@ceid.upatras.gr');
    }

    protected function verificationUrl()
    {
        return URL::temporarySignedRoute(
            'register',
            now()->addMinutes(config('auth.verification.expire', 60)),
            ['username' => $this->username]
        );
    }
}
