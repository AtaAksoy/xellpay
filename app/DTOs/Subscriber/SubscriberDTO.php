<?php

namespace App\DTOs\Subscriber;

use App\Traits\ArrayConvertable;

class SubscriberDTO
{

    use ArrayConvertable;

    public ?int $subscriberNo = null;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $token
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            token: null
        );
    }
    
}
