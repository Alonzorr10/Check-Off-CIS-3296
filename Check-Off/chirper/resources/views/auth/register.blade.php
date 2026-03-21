@extends('layouts.web')

@section('content')
<div class='flex flex-1 flex-col items-center justify-center gap-5'>
    <form id="firebase-register-form">
        @csrf
        <div class='flex flex-col gap-5 items-center justify-center'>
            <div><label for="name">name</label></div>
            <div><input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            /></div>
            <x-input-error :messages="$errors->get('name')"/>
            <div><label for="email">email</label></div>
            <div><input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            /></div>
            <x-input-error :messages="$errors->get('email')" />
            <div><label for="password">password</label></div>
            <div><input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            /></div>
            <x-input-error :messages="$errors->get('password')"/>
            <div><label for="password_confirmation">confirm password</label></div>
            <div><input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            /></div>
            <x-input-error :messages="$errors->get('password_confirmation')"/>
            <div><button type="submit">register</button></div>
        </div>
    </form>
    <div class='flex'><a href="{{ route('login') }}">{{ __('Already registered?') }}</a></div>
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js';
        import {
            getAuth,
            createUserWithEmailAndPassword,
            updateProfile
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

        async function sendTokenToLaravel(token, csrf) {
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
                throw new Error(data.error || data.message || 'Registration failed');
            }

            window.location.href = data.redirect || '/dashboard';
        }

        document.getElementById('firebase-register-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const csrf = document.querySelector('input[name="_token"]').value;

            if (password !== passwordConfirmation) {
                alert('Passwords do not match.');
                return;
            }

            try {
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);

                if (name) {
                    await updateProfile(userCredential.user, {
                        displayName: name
                    });
                }

                const token = await userCredential.user.getIdToken(true);
                await sendTokenToLaravel(token, csrf);
            } catch (error) {
                console.error(error);
                alert(error.message);
            }
        });
    </script>
</div>
@endsection
