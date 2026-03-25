@extends('layouts.logged')

@section('content')
@vite(['resources/js/events.js'])
</head>
<body>
    <div id="user-events">Your Events</div>

    <div id="new-event">
        <div class='flex items-center justify-center mb-5'><button id="create-event" class="create-event" onclick="window.addNewEventBlock()"> Create New Event</button></div>

        <div id="event-container"class="space-y-4"></div>
    </div>




    {{-- <script src="{{ asset('resources/js/events.js') }}"></script> --}}
</body>
</html>
@endsection