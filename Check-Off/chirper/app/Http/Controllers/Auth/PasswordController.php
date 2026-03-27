<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseCurrentPasswordVerifier;
use App\Services\FirebasePasswordSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function __construct(
        private FirebasePasswordSyncService $firebasePasswordSyncService,
        private FirebaseCurrentPasswordVerifier $firebaseCurrentPasswordVerifier
    ) {
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $isCurrentPasswordValid = $this->firebaseCurrentPasswordVerifier->verify(
            $request->user()->email,
            $validated['current_password']
        );

        if (! $isCurrentPasswordValid) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ])->errorBag('updatePassword');
        }

        $this->firebasePasswordSyncService->syncPassword(
            $request->user(),
            $validated['password']
        );

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}