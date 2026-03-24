@extends('layouts.web')

@section('content')
<div class="flex-1 flex flex-col justify-center items-center bg-stone-100 px-4 min-h-full w-full">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="w-full sm:max-w-md px-6 py-8 bg-stone-900 shadow-2xl rounded-2xl border border-stone-800">

        <form id="firebase-email-login-form" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-stone-300" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-stone-300" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div class="flex flex-col gap-4 mt-6">
                <x-primary-button class="w-full justify-center py-3 bg-stone-600 hover:bg-stone-500 text-white border-none" type="submit">
                    {{ __('LOG IN WITH EMAIL') }}
                </x-primary-button>

                <div class="text-center">
                    <a class="underline text-sm text-stone-400 hover:text-stone-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" href="{{ route('register') }}">
                        {{ __('Need an account? Register') }}
                    </a>
                </div>
            </div>
        </form>

        {{-- Google Login Section --}}
        <div class="mt-8 pt-6 border-t border-stone-800">
            <x-primary-button
                id="google-login-btn"
                type="button"
                class="w-full justify-center py-3 bg-stone-100 hover:bg-white text-stone-900 border-none"
            >
                {{ __('SIGN IN WITH GOOGLE') }}
            </x-primary-button>
        </div>
    </div>
</div>

<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js';
    import {
        getAuth,
        GoogleAuthProvider,
        signInWithPopup,
        signInWithEmailAndPassword
    } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js';

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

    const csrf =
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;

    async function sendTokenToLaravel(token) {
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

        if (!response.ok) {
            throw new Error(data.error || data.message || 'Login failed');
        }

        window.location.href = data.redirect || '/dashboard';
    }

    document.getElementById('google-login-btn').addEventListener('click', async () => {
        try {
            const result = await signInWithPopup(auth, provider);
            const token = await result.user.getIdToken();
            await sendTokenToLaravel(token);
        } catch (error) {
            console.error(error);
            alert(error.message);
        }
    });

    document.getElementById('firebase-email-login-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        try {
            const userCredential = await signInWithEmailAndPassword(auth, email, password);
            const token = await userCredential.user.getIdToken();
            await sendTokenToLaravel(token);
        } catch (error) {
            console.error(error);
            alert(error.message);
        }
    });
</script>
@endsection