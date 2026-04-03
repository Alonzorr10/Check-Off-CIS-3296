@extends('layouts.web')

@section('content')
@vite(['resources/js/events.js'])
</head>
<body>
    <div class="max-w-5xl mx-auto py-12 px-6">
    
    <div id="user-events">Your Events</div>

    <div id="new-event">
        <button id="create-event" class="create-event" onclick="window.addNewEventBlock()"> Create New Event</button>

        <div id="event-container"class="space-y-4"></div>
    </div>
    </div>
</body>
</html>
@endsection