<nav class="navbar">
    <ul class="nav-list">
        <li class="nav-item @if(request()->is('/')) active @endif">
            <a class="nav-link" href="{{ route('index') }}">Home</a>
        </li>
        <li class="nav-item @if(request()->is('login')) active @endif">
            <a class="nav-link" href="{{ route('login_page') }}">Login</a>
        </li>
        @if (isset($room) && request()->is('room/' . $room->uuid))
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('room_enter', $room->uuid) }}">{{ $room->name }}</a>
            </li>
        @else
            @isset($_COOKIE['lastRoom'])
                @php $room = App\Room::find($_COOKIE['lastRoom']) @endphp
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('room_enter', $room->uuid) }}">{{ $room->name }}</a>
                </li>
            @endisset
        @endif
    </ul>
</nav>