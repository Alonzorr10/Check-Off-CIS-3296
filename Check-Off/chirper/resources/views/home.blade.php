@extends('layouts.web')

@section('content')
<div class='flex-1 flex flex-col justify-center items-center text-stone-900 gap-5 text-xl'>
    <div class='flex w-full py-16 flex-col justify-center items-center text-stone-100 bg-stone-700'>
        <h1 class='text-7xl font-bold '>Check-Off</h1>
        <p class=''>Pay just the right amount.</p>
    </div>
    <div class='flex flex-1 w-full divide-x divide-stone-500'>
        <div class='flex-1 flex flex-col items-center ml-5 gap-10 pr-5'>
            <div class='text-2xl p-3'>Event participant features</div>
            <div class='flex items-center justify-between gap-5'>
                1. Enter code the organizer shared with you
                <img src='{{ asset("code.png") }}' class='shadow w-1/3'></img>
            </div>
            <div class='flex items-center justify-between gap-5 '>
                2. Once you are sure you have paid, let the organizer know
                <img src='{{ asset("debt.png") }}' class='shadow w-2/3'></img>
            </div>
            <div class='flex items-center justify-between gap-5'>
                3. <b>(user exclusive)</b> Build your streaks and compete with your friends!
                <div class='flex flex-col w-3/5 gap-5'>
                    <img src='{{ asset("streak.png") }}' class='shadow'></img>
                    <a href="{{ route('register') }}" class='font-semibold mx-auto bg-stone-700 text-white text-lg rounded p-3 text-center hover:scale-95 transition-transform'>join us today!</a>
                </div>
            </div>
        </div>
        <div class='flex-1 flex flex-col items-center mr-5 gap-10 pl-5'>
            <div class='text-2xl inline-flex items-center gap-5 p-1'><b>(user exclusive) </b>Event organizer features <a href="{{ route('register') }}" class='text-lg rounded p-3 bg-stone-700 text-white  hover:scale-95 transition-transform font-semibold'>join us today!</a></div>
            <div class='flex items-center justify-between'>
                1. Create an event; code will be automatically generated for you
                <img src='{{ asset("init.png") }}' class='shadow w-1/3'></img>
            </div>
            <div class='flex items-center justify-between gap-5'>
                2. Assign each person charging amount for some purchase and set deadline
                <img src='{{ asset("details.png") }}' class='shadow w-1/2'></img>
            </div>
            <div class='flex items-center justify-between gap-5'>
                3. You can check assignees' payment status
                <img src='{{ asset("waiting.png") }}' class='shadow w-1/2'></img>
            </div>
            <div class='flex items-center justify-between gap-5'>
                2. Once you are sure you got paid, confirm it
                <img src='{{ asset("confirm.png") }}' class='shadow w-1/2'></img>
            </div>
        </div>
    </div>
</div>
@endsection