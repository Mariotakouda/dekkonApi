<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $code,
    ) {}

    public function build(): self
    {
        return $this->subject('Votre code de vérification DEKKON')
            ->view('emails.verify-account')
            ->with([
                'user' => $this->user,
                'code' => $this->code,
            ]);
    }
}
