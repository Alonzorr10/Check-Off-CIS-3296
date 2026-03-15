<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Check-Off')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chiron+GoRound+TC:wght@200..900&display=swap" rel="stylesheet">
        <style>body {font-family: 'Chiron GoRound TC', sans-serif;}</style>
    </head>
    <body class="min-h-screen flex flex-col">
        @include('partials.navbar')
        <main class="flex-1 flex flex-col justify-center items-center w-full">
            @yield('content')
        </main>
    </body>
</html>