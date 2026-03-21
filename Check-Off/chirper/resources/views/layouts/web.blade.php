<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,interactive-widget=resizes-content">
        <title>@yield('title', 'Check-Off')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chiron+GoRound+TC:wght@200..900&display=swap" rel="stylesheet">
    </head>
    <body class="flex flex-col w-full h-[100dvh]">
        <div class='flex flex-col flex-1 mx-5 overflow-hidden'>
            @include('partials.navbar')
            @yield('content')
            <div class='md:hidden flex flex-row justify-between shrink-0 my-5'>
                @auth
                    {{-- Links for logged-in users --}}
                    <a href="/events" class="{{ request()->routeIs('home') ? 'active' : '' }}">Events</a>
                    <a href="/contributions-logged-in" class="">Contributions</a>
                    <form method="POST" action="{{ route('logout') }}" class=''>
                        @csrf
                        <button type="submit" class="">Logout</button>
                    </form>
                    <a href='/profile' class='inline-flex items-center justify-center w-9 h-9 rounded-full'>{{strtoupper(substr(Auth::user()->name,0,1))}}</a>
                @endauth

                @guest
                    {{-- Links for visitors --}}
                    <a href="{{route('home')}}" class='{{request()->routeIs("home")?"active":""}}'>Home</a>
                    <a href="{{route('contributions')}}" class='{{request()->routeIs("contributions")?"active":""}}'>Contributions</a>
                    <a href="{{ route('login') }}" class='{{request()->routeIs("login")?"active":""}}'>Login</a>
                    <a href="{{ route('register') }}" class='{{request()->routeIs("register")?"active":""}}'>Sign Up</a>
                @endguest
            </div>
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