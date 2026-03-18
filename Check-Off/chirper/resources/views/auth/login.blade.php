<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="firebase-email-login-form">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
            />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-lg text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-stone-800" href="{{ route('register') }}">
                {{ __('Need an account? Register') }}
            </a>

            <x-primary-button class="ms-3" type="submit">
                {{ __('Log in with Email') }}
            </x-primary-button>
        </div>
    </form>

    <div class="flex items-center justify-center mt-6">
        <x-primary-button id="google-login-btn" type="button">
            {{ __('Sign in with Google') }}
        </x-primary-button>
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
</x-guest-layout>