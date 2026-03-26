@extends('layouts.logged')

@section('content')
@vite(['resources/js/contributions-logged.js'])
<div class="max-w-2xl mx-auto py-8 px-4">
    <div id="user-contribution-container">
        <div class="text-stone-500 text-center animate-pulse py-20 text-sm italic">
            Syncing your debts from the cloud...
        </div>
    </div>
</div>
@endsection