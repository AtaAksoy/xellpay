<?php

namespace App\DTOs\Bill;

class PaymentResponseDTO
{
    public function __construct(
        public readonly bool $status,
        public readonly string $message,
        public readonly ?int $remaining = null
    ) {}
}
