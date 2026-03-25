@extends('layouts.logged')

@section('content')
<div class='flex items-center justify-center my-10'><div class='nav-link inline-flex items-center justify-center w-20 h-20 rounded-full ring ring-stone-800 transition-all text-4xl'>{{strtoupper(substr(Auth::user()->name,0,1))}}</div></div>
<div class='flex-1 w-full'><div>streaks</div></div>
@endsection