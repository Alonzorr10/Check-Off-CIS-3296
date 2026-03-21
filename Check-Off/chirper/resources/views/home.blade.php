@extends('layouts.web')

@section('content')
<div class='flex basis-1/4 md:basis-1/3 lg:basis-1/3 flex-col justify-center items-center'>
    <h1 class='text-5xl md:text-7xl lg:text-7xl'>Check-Off</h1>
    <p class=''>Pay just the right amount.</p>
</div>
<div class='flex flex-1 flex-col w-full'>
    <div class='flex flex-col md:flex-row lg:flex-row md:justify-between md:items-center flex-1'>
        <h2 class='text-2xl md:text-3xl'>Event Organization</h2>
        <p>Members can organize events and send links to participants!</p>
    </div>
    <div class='flex flex-col md:flex-row lg:flex-row flex-1 md:justify-between md:items-center '>
        <h2 class='text-2xl md:text-3xl'>Instant Bill-Split</h2>
        <p>Enter the total amount for each category, and assign charges by expense category.</p>
    </div>
    <div class='flex flex-col md:flex-row lg:flex flex-1 md:justify-between md:items-center'>
        <h2 class='text-2xl md:text-3xl'>Streak Rewards</h2>
        <p>Compete streak scores with your friends to get exclusive rewards!</p>
    </div>
</div>
@endsection