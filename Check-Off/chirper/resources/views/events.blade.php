@extends('layouts.logged')

@section('content')
</head>
<body>
    <div id="user-events">Your Events</div>

    <div id="new-event">
        <button id="create-event" class="create-event" onclick="addNewEventBlock()"> Create New Event</button>

        <div id="event-container"class="space-y-4"></div>
    </div>



    
    <script src="{{ asset('storage/customjs/events.js') }}"></script>
</body>
</html>
@endsection