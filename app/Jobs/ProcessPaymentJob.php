<?php

namespace App\Jobs;

use App\Actions\Payment\ConfirmPaymentAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $transactionReference
    ) {}

    public function handle(ConfirmPaymentAction $action): void
    {
        $action->execute($this->transactionReference);
    }
}
