<?php

use Illuminate\Mail\Transport\ResendTransport;
use Illuminate\Support\Facades\Mail;

it('resolves the resend mailer with the resend api key', function () {
    config(['services.resend.key' => 're_'.fake()->regexify('[A-Za-z0-9]{24}')]);

    $transport = Mail::mailer('resend')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(ResendTransport::class)
        ->and((string) $transport)->toBe('resend');
});
