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
    <body class="flex flex-col w-full min-h-screen h-screen max-h-screen">
        @include('partials.navbar')

        <div class='flex-1 flex'>
            @yield('content')
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded',()=>{
            document.querySelectorAll('input[maxlength="1"]').forEach((input,idx,inputs)=>{
                input.addEventListener('input',()=>{
                    if (input.value.length===1&&idx<inputs.length-1){
                        inputs[idx+1].focus()
                    }
                })
            })
        })
        </script>
    </body>
</html>