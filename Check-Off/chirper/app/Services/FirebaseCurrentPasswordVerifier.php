<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebaseCurrentPasswordVerifier
{
    public function verify(string $email, string $plainPassword): bool
    {
        $apiKey = env('VITE_FIREBASE_API_KEY');

        if (!$apiKey) {
            throw new RuntimeException('Firebase API key is not configured.');
        }

        $response = Http::post(
            'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key='.$apiKey,
            [
                'email' => $email,
                'password' => $plainPassword,
                'returnSecureToken' => true,
            ]
        );

        return $response->successful();
    }
}