@extends('layouts.web')

@section('content')
<div class='flex flex-1 flex-col items-center justify-center gap-5'>
    <x-auth-session-status :status="session('status')" />
    <form id="firebase-email-login-form flex flex-col items-center justify-center gap-5">
        @csrf
        <div class='flex flex-col gap-5 items-center justify-center'>
            <div><label for="email">email</label></div>
            <div><input
                id="email"
                class=""
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
            /></div>
            <div><label for="password">password</label></div>
            <div><input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            /></div>
            <div><button type="submit">log in with email</button></div>
        </div>
    </form>
    <div class='flex'><button id="google-login-btn" type="button">sign in with google</button></div>
    <div class='flex'><a href="{{ route('register') }}">{{ __('Need an account? Register') }}</a></div>
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
</div>
@endsection