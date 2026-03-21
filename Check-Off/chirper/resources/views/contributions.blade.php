@extends('layouts.web')

@section('content')
<div class='flex flex-col md:flex-row flex-1 overflow-hidden'>
  <div class='flex-1 flex flex-col items-center justify-center gap-5'>
    <div>enter event code</div>
    <form>
      <div class='flex flex-col gap-5 items-center justify-center'>
        <div>
          <div><input id='code1' name='code1' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
          <div><input id='code2' name='code2' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
          <div><input id='code3' name='code3' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
          <div><input id='code4' name='code4' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
          <div><input id='code5' name='code5' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
          <div><input id='code6' name='code6' class='rounded w-[4ch] text-center' maxlength='1' oninput='this.value=this.value.toUpperCase()' required/></div>
        </div>
        <div><input id='name' placeholder='Enter your name' autocomplete='on' required/></div>
        <div><button popovertarget='event_details' type='submit' class=''>join</button></div>
      </div>
    </form>
  </div>
  <div class='hidden flex-1 md:flex flex-col items-center gap-5 h-[calc(100dvh-4rem)] overflow-y-auto'>
    <div class=''>my contributions</div>
    <div class='grid grid-cols-2 gap-5 min-h-0'>
    {{--@foreach($events as $e)--}}
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button popovertarget='event_details' type='button' class=''>pay</button></div>
      </form>
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button type='submit' class=''>pay</button></div>
      </form>
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button type='submit' class=''>pay</button></div>
      </form>
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button type='submit' class=''>pay</button></div>
      </form>
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button type='submit' class=''>pay</button></div>
      </form>
      <form class='basis-48 flex flex-col gap-5 items-center justify-center'>
        <div>temp{{--{{$e->name}}--}}</div>
        <div>AAAAAA{{--{{$e->code}}--}}</div>
        <div>$1k{{--{{$e->charge}}--}}</div>
        <div class='flex'><input id='charge' type='number' class='flex-1 rounded' required/></div>
        <div><button type='submit' class=''>pay</button></div>
      </form>
    {{--@endforeach--}}
    </div>
  </div>
</div>
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
@endsection
