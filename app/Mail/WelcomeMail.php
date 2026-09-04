<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function build(): self
    {
        return $this->subject('Bienvenue sur DEKKON')
            ->view('emails.welcome')
            ->with(['user' => $this->user]);
    }
}
