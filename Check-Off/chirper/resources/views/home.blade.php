@extends('layouts.web')
@section('content')
    <div class='flex flex-1 flex-col justify-center items-center'>
        <h1 class='text-7xl font-bold text-stone-700'>Check-Off</h1>
        <p class='text-stone-800'>Pay just the right amount.</p>
    </div>
    <div class='flex flex-1 flex-col justify-around items-center bg-stone-700 w-full'>
        <div class='flex flex-1 justify-around items-center w-full'>
            <h2 class='text-3xl font-bold text-white'>Event Organization</h2>
            <p class='text-white'>Members can organize events and send links to participants!</p>
        </div>
        <div class='flex flex-1 justify-around items-center w-full'>
            <p class='text-white'>Enter the total amount for each category, and assign charges by expense category.</p>
            <h2 class='text-3xl font-bold text-white'>Instant Bill-Split</h2>
        </div>
        <div class='flex flex-1 justify-around items-center w-full'>
            <h2 class='text-3xl font-bold text-white'>Streak Rewards</h2>
            <p class='text-white'>Compete streak scores with your friends to get exclusive rewards!</p>
        </div>
    </div>
@endsection