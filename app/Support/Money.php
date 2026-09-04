<?php

namespace App\Support;

class Money
{
    public function __construct(
        private readonly float $amount,
        private readonly string $currency = 'XOF',
    ) {}

    public static function fromFloat(float $amount, string $currency = 'XOF'): self
    {
        return new self(round($amount, 2), $currency);
    }

    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        return new self(max(0, $this->amount - $other->amount), $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self(round($this->amount * $factor, 2), $this->currency);
    }

    public function value(): float
    {
        return $this->amount;
    }

    public function format(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' ' . $this->currency;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
