<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class FirebasePasswordSyncService
{
    public function __construct(
        private FirebaseAuth $firebaseAuth
    ) {
    }

    public function syncPassword(User $user, string $plainPassword): void
    {
        try {
            $firebaseUser = $this->firebaseAuth->getUserByEmail($user->email);
        } catch (UserNotFound $e) {
            $this->firebaseAuth->createUser([
                'email' => $user->email,
                'emailVerified' => true,
                'password' => $plainPassword,
                'displayName' => $user->name,
            ]);

            return;
        }

        $this->firebaseAuth->changeUserPassword($firebaseUser->uid, $plainPassword);
    }
}