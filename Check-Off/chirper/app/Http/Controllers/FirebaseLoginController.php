<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class FirebaseLoginController extends Controller
{
    public function __construct(private FirebaseAuth $firebaseAuth)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $this->firebaseAuth->getUser($uid);

            $user = User::firstOrCreate(
                ['email' => $firebaseUser->email],
                [
                    'name' => $firebaseUser->displayName ?: 'Firebase User',
                    'password' => bcrypt(str()->random(32)),
                ]
            );

            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login Successful',
                'redirect' => route('dashboard'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Invalid Firebase Token',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}