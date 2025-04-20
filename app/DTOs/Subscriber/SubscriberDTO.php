<?php

namespace App\DTOs\Subscriber;

class SubscriberDTO
{
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

    public function toArray() : array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->token
        ];
    }
}
