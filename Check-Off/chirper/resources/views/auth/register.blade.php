@extends('layouts.web')

@section('content')
<div class="flex-1 flex flex-col justify-center items-center  px-4 min-h-full w-full py-12">

    <div class="w-full sm:max-w-md px-6 py-8 bg-stone-900 shadow-2xl rounded-2xl border border-stone-800">

        <h2 class="text-2xl font-bold text-white mb-6 text-center">Create Account</h2>

        <form id="firebase-register-form" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-stone-300" />
                <x-text-input
                    id="name"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-stone-300" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-stone-300" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-stone-300" />
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full bg-stone-950 border-stone-700 text-white"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-4 mt-8">
                <x-primary-button class="w-full justify-center py-3 bg-stone-600 hover:bg-stone-500 text-white border-none" type="submit">
                    {{ __('REGISTER') }}
                </x-primary-button>

                <div class="text-center">
                    <a class="underline text-sm text-stone-400 hover:text-stone-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" href="{{ route('login') }}">
                        {{ __('Already registered? Log in') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

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
@endsection