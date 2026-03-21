<nav class="md:flex shrink-0 hidden justify-start gap-10 my-5">
    @auth
    {{-- Links for logged-in users --}}
    <a href="/events" class="{{ request()->routeIs('home') ? 'active' : '' }}">Events</a>
    <a href="/contributions-logged-in" class="">Contributions</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="">Logout</button>
    </form>
    <a href='/profile' class='inline-flex items-center justify-center w-9 h-9 rounded-full'>
        {{strtoupper(substr(Auth::user()->name,0,1))}}
    </a>
    @endauth

    @guest
    {{-- Links for visitors --}}
    <a href="{{route('home')}}" class='{{request()->routeIs("home")?"active":""}}'>Home</a>
    <a href="{{route('contributions')}}" class='{{request()->routeIs("contributions")?"active":""}}'>Contributions</a>
    <a href="{{ route('login') }}" class='{{request()->routeIs("login")?"active":""}}'>Login</a>
    <a href="{{ route('register') }}" class='{{request()->routeIs("register")?"active":""}}'>Sign Up</a>
    @endguest
</nav>