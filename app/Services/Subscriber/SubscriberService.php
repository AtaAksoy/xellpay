<?php

namespace App\Services\Subscriber;

use App\DTOs\Subscriber\SubscriberDTO;
use App\Http\Requests\SubscriberLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SubscriberService
{

    public function checkSubscriber(int $subscriberId) : bool {
        return User::where('id', $subscriberId)->exists();
    }

    public function getSubscriber(int $subscriberId) : ?User {
        return User::where('id', $subscriberId)->first();
    }

    public function create(SubscriberDTO $subscriber): ?SubscriberDTO
    {
        $user = User::create([
            'name' => $subscriber->name,
            'email' => $subscriber->email,
            'password' => Hash::make($subscriber->password),
        ]);
        $token = $user->createToken('xellpay-auth')->plainTextToken;
        $dto = new SubscriberDTO($user->name, $user->email, null, $token);
        if (!$user)
            return null;
        return $dto;
    }

    public function login(SubscriberLoginRequest $request): ?SubscriberDTO
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('xellpay-auth')->plainTextToken;
            $dto = new SubscriberDTO($user->name, $user->email, null, $token);

            return $dto;
        } else
            return null;
    }
}
