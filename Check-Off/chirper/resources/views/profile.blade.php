@extends('layouts.web')
@section('content')
<div class='flex items-center justify-between gap-5'><div class='bg-stone-400 rounded-xl p-6 ring ring-stone-100'><h1 class='text-lg font-semibold text-stone-900'>{{ Auth::user()->name }}</h1></div><div><img></img></div></div>
@endsection