<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $token,
    ) {}

    public function build(): self
    {
        return $this->subject('Réinitialisation de votre mot de passe DEKKON')
            ->view('emails.reset-password')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
