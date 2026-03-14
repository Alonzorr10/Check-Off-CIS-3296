@extends('layouts.web')

@section('content')

    <div class="page" id="page-guest">
    <div class="guest-box">
      <div style="text-align:center;margin-bottom:1rem">
        <div class="page-title">Join an Event</div>
        <div class="page-sub">Enter your invite code to view and settle your contributions</div>
      </div>
      <div class="card" id="guestLookupCard">
        <input class="guest-input" id="guestCode" placeholder="ABC123" maxlength="6" oninput="this.value=this.value.toUpperCase()">
        <div style="margin-bottom:8px">
          <div class="form-label" style="margin-bottom:4px">Your name</div>
          <input class="form-input" id="guestName" placeholder="e.g. Sam">
        </div>
        <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="guestLookup()">View My Contributions</button>
      </div>
      <div id="guestResult"></div>
      <div style="text-align:center;margin-top:1rem;font-size:13px;color:#888">
        Have an account? <span style="color:#1D9E75;cursor:pointer">Log in</span>
      </div>
    </div>
  </div>
</div>
@endsection
    
