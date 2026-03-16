@extends('layouts.web')

@section('content')
<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex items-center justify-center mt-6">
        <x-primary-button id="google-login-btn" type="button">
            {{ __('Sign in with Google') }}
        </x-primary-button>
    </div>

    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js';
        import { getAuth, GoogleAuthProvider, signInWithPopup } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js';

        const firebaseConfig = {
            apiKey: "{{ env('VITE_FIREBASE_API_KEY') }}",
            authDomain: "{{ env('VITE_FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('VITE_FIREBASE_PROJECT_ID') }}",
            storageBucket: "{{ env('VITE_FIREBASE_STORAGE_BUCKET') }}",
            messagingSenderId: "{{ env('VITE_FIREBASE_MESSAGING_SENDER_ID') }}",
            appId: "{{ env('VITE_FIREBASE_APP_ID') }}",
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        const button = document.getElementById('google-login-btn');

        button.addEventListener('click', async () => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            try {
                const result = await signInWithPopup(auth, provider);
                const token = await result.user.getIdToken();

                const response = await fetch('/firebase/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ token }),
                });

                const data = await response.json();
                console.log(data);

                if (!response.ok) {
                    throw new Error(data.error || data.message || 'Failed to log in');
                }

                window.location.href = data.redirect || '/dashboard';
            } catch (error) {
                console.error(error);
                alert(error.message);
            }
        });
    </script>
</x-guest-layout>
@endsection