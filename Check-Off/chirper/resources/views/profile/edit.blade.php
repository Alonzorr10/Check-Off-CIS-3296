<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-stone-800 dark:text-stone-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 justify-center justify-items-center justify-self-center content-center items-center self-center place-content-center place-items-center place-self-center">
        <div class="max-w-7xl  sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-stone-200 dark:bg-stone-800 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-stone-200 dark:bg-stone-800 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-stone-200 dark:bg-stone-800 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
