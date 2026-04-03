@extends('layouts.web')

@section('content')
@vite(['resources/js/contributions-logged.js'])

<div class="max-w-5xl mx-auto py-10 px-4 space-y-8 items-center justify-center flex flex-col">
    <div class='flex items-center justify-center text-2xl font-semibold text-stone-900'>
        Welcome back, {{ Auth::user()->name }}!
    </div>

    <a href="{{ route('profile.edit') }}" class='my-0'><div class='rounded-full w-20 h-20 flex items-center justify-center text-4xl font-bold bg-stone-800 text-white hover:scale-95 transition-transform'>
        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
    </div></a>

    <div class="bg-stone-900 border border-stone-800 rounded-2xl p-6 w-full">
        <h1 class="text-2xl font-bold text-white">My Settlement Streak</h1>
        <p class="text-stone-300 mt-2">
            Your streak grows when you repay accepted debts on time. It resets if a payment is denied or if a debt stays unpaid past its due date.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="card-label">Current Streak</div>
                <div id="streak-current" class="card-value">0</div>
            </div>

            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="card-label">Best Streak</div>
                <div id="streak-best" class="card-value">0</div>
            </div>

            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="card-label">Last Result</div>
                <div id="streak-last-result" class="text-white text-xl font-bold mt-3 lowercase">none</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 w-full">
        <div class="bg-stone-900 rounded-2xl p-6">
            <div class="card-label">Total Owed</div>
            <div id="total-owed" class="card-value text-red-400">¥0</div>
        </div>
        <div class="bg-stone-900 rounded-2xl p-6">
            <div class="card-label">Active Events</div>
            <div id="event-count" class="card-value">0</div>
        </div>
        <div class="bg-stone-900 rounded-2xl p-6">
            <div class="card-label">Awaiting Confirmation</div>
            <div id="pending-count" class="card-value text-amber-400">0</div>
        </div>
    </div>
</div>
@endsection