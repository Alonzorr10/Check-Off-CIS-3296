@extends('layouts.logged')

@section('content')
@vite(['resources/js/contributions.js'])
<div class="max-w-5xl mx-auto py-10 px-4 space-y-8">
    <div class="bg-stone-900 border border-stone-800 rounded-2xl p-6">
        <h1 class="text-2xl font-bold text-white">My Settlement Streak</h1>
        <p class="text-stone-400 mt-2">
            Your streak grows when you repay accepted debts on time. It resets if a payment is denied or if a debt stays unpaid past its due date.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="text-stone-400 text-sm">Current Streak</div>
                <div id="streak-current" class="text-white text-3xl font-bold mt-2">0</div>
            </div>

            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="text-stone-400 text-sm">Best Streak</div>
                <div id="streak-best" class="text-white text-3xl font-bold mt-2">0</div>
            </div>

            <div class="bg-stone-950 border border-stone-800 rounded-xl p-4">
                <div class="text-stone-400 text-sm">Last Result</div>
                <div id="streak-last-result" class="text-white text-xl font-bold mt-3 lowercase">none</div>
            </div>
        </div>
    </div>

    <div class="bg-stone-900 border border-stone-800 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">My Contributions</h2>
        <div id="contributions-container">
            <div class="text-stone-500 text-sm italic">Loading your contributions...</div>
        </div>
    </div>
</div>
@endsection