@extends('layouts.web')

@section('content')
@vite(['resources/js/contributions.js'])
<form class='flex-1 flex flex-col items-center justify-center gap-5'>
  <div class=''>enter event code below</div>
  <div class='font-bold'>↓</div>
  <div class='space-x-1'>
    <input name='code1' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1' oninput='this.value=this.value.toUpperCase()'/>
    <input name='code2' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code3' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code4' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code5' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code6' class='rounded ring ring-stone-400 border-0 w-[5ch] text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
  </div>
  <div><input id='name' class='rounded-2xl ring ring-stone-400 border-0 text-center focus:outline-none focus:ring focus:ring-black focus:border-transparent' placeholder='Enter your name' autocomplete='true'/></div>
  <div class='font-bold'>↓</div>
  <div class='bg-emerald-600 text-white px-6 py-1 rounded-3xl font-bold shadow-lg hover:scale-95 transition-transform hover:outline-none hover:ring hover:ring-black hover:border-transparent'>
  <button id="view-contributions" type="button" class="">View Contributions Owed</button></div>
</form>
<div class='flex-1 flex flex-col items-center'>
  <div class='h-1/6 flex items-center justify-center'>My Contributions</div>
  <div class='flex-1 flex-wrap w-full'>
    <div id="contributions-container">
    </div>
      <div class='flex-1 flex flex-col items-start justify-center ring ring-stone-400'></div>
  </div>
</div>

{{-- <script src="{{ asset('resources/js/contributions.js') }}"></script> --}}
@endsection
