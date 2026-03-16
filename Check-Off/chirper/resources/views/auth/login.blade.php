<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- <form method="POST" action="{{ route('login') }}"> --}}
    <form id="firebase-login-form">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-stone-900 border-stone-300 dark:border-stone-700 text-yellow-600 bg-stone-200 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:focus:ring-offset-stone-800" name="remember">
                <span class="ms-2 text-lg text-stone-600 dark:text-stone-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-lg text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-stone-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3" type="submit">
            {{-- <x-primary-button class="ms-3"> --}}
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- changed from FB Doc --}}
    <script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js';
    import { getAuth, signInWithEmailAndPassword } from 'https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js';

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

    const form = document.getElementById('firebase-login-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const csrf = document.querySelector('input[name="_token"]').value;

        try {
            const userCredential = await signInWithEmailAndPassword(auth, email, password);
            const token = await userCredential.user.getIdToken();

            const response = await fetch('/firebase/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ token: token }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to Login!');
            }

            window.location.href = data.redirect || '/home';
        } catch (error) {
            alert(error.message);
        }
    });
    </script>
</x-guest-layout>