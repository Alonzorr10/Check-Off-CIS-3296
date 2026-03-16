<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://fonts.googleapis.com/css2?family=Chiron+GoRound+TC:wght@200..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body {font-family: 'Chiron GoRound TC', sans-serif;}</style>
</head>
<body>
    <button onclick="generateRandomButton(4)">Testing</button>
    <script src="{{ asset('storage/customjs/events.js') }}"></script>
</body>
</html>