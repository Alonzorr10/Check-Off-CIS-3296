@extends('layouts.web')

@section('content')
<!--
  <div class="page flex flex-col flex-1 items-center justify-around text-lg" id="page-guest">
    <div class="guest-box flex flex-col flex-1 items-center justify-around">
      <div class='flex flex-1 flex-col'>
        <div class="flex-1 page-title">Join an Event</div>
        <div class="flex-2 page-sub">Enter your invite code to view and settle your contributions</div>
      </div>
      <div class="card flex-1 flex flex-col" id="guestLookupCard">
        <input class="guest-input flex-1" id="guestCode" placeholder="ABC123" maxlength="6" oninput="this.value=this.value.toUpperCase()">
        <div class='flex flex-1'>
          <div class="form-label flex-1" >Your name</div>
          <input class="form-input flex-1" id="guestName" placeholder="e.g. Sam">
        </div>
        <button class="flex btn btn-primary" onclick="guestLookup()">View My Contributions</button>
      </div>
      <div class='flex flex-1' id="guestResult"></div>
      <div class='flex flex-1'>
        Have an account? <span >Log in</span>
      </div>
    </div>
  </div>
</div>
-->
<form class='flex-1 flex flex-col items-center justify-evenly'>
<!--
  <div class='flex flex-1 flex-col gap-2'>
    <label for='code' class='flex-1'>Code:</label>
    <input name='code' class='flex-2 rounded-2xl bg-stone-100 border-0' placeholder='Enter code' oninput='this.value=this.value.toUpperCase()'/>
  </div>
-->
  <div>
    <input name='code1' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1' oninput='this.value=this.value.toUpperCase()'/>
    <input name='code2' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code3' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code4' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code5' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
    <input name='code6' class='rounded bg-stone-100 border-0 w-[4ch] text-center' maxlength='1' minlength='1'oninput='this.value=this.value.toUpperCase()'/>
  </div>
  <input id='name' class='rounded-2xl bg-stone-100 border-0 text-center' placeholder='Enter your name' autocomplete='true'/>
</form>
<div class='flex-1 flex flex-col items-center'>
  <div class='flex-1'>my contributions</div>
  <div class='flex-1 flex-wrap'>
    {{--@foreach($events as $e)--}}
      <div class='flex-1 flex flex-col items-start justify-center bg-stone-400'>
        <div class='flex-1 flex items-start justify-center'>temp{{--{{$e->name}}--}}</div>
        <div class='flex-1 flex items-start justify-center'>$1k{{--{{$e->charge}}--}}</div>
      </div>
    {{--@endforeach--}}
  </div>
</div>
@endsection
