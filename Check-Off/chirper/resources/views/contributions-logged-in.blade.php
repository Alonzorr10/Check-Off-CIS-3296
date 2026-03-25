@extends('layouts.logged')

@section('content')
<div class='flex-1 flex flex-col items-center w-full'>
  <div class='h-1/3 flex items-center justify-center'>My Contributions</div>
  <div class='flex-1 flex-wrap w-full'>
    <div id="contributions-container">
    </div>
      <div class='flex-1 flex flex-col items-start justify-center'></div>
  </div>
</div>

{{-- <script src="{{ asset('resources/js/contributions.js') }}"></script> --}}
@endsection