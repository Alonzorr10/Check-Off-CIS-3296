@extends('layouts.web')

@section('content')
@vite(['resources/js/contributions.js'])

<form class='flex-1 flex flex-col items-center justify-evenly'>
  <div>
    <input name='code1' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1' oninput='this.value=this.value.toUpperCase()'/>
    <input name='code2' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code3' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code4' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code5' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code6' class='rounded bg-stone-400 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
  </div>
  <input id='name' class='rounded-2xl bg-stone-400 border-0 text-center' placeholder='Enter your name' autocomplete='true'/>

  <button id="view-contributions" type="button" class="bg-emerald-600 text-white px-6 py-1 rounded-2xl font-bold mt-4 shadow-lg active:scale-95 transition-transform">View Contributions Owed</button>
</form>
<div class='flex-1 flex flex-col items-center'>
  <div class='flex-1'>My Contributions</div>
  <div class='flex-1 flex-wrap'>
    <div id="contributions-container">
    </div>
      <div class='flex-1 flex flex-col items-start justify-center bg-stone-400'>
      </div>
  </div>
</div>

{{-- <script src={{ asset('resources/js/contributions.js') }}><script> --}}
@endsection
