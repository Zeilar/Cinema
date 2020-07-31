@extends('head')

@section('body')
    <div class="wrapper-index">
        <div class="user-welcome">
            <span>Welcome</span>
            <span class="username" style="background: {{ $user->color }};">
                {{ $user->username }}
            </span>
        </div>

        <form class="new-room" action="{{ route('room_create') }}" method="post">
            @csrf
            <input type="text" name="roomName" id="room-name-input" placeholder="Room name" autocomplete="off">
            <button class="btn room-create" type="submit">
                <span class="original-text">Create new room</span>
            </button>
        </form>

        <div class="existing-rooms">
            @foreach ($rooms as $room)
                <a class="btn room-join" href="{{ route('room_enter', $room->uuid) }}">
                    <span>{{ $room->name }}</span>
                    <div class="circle"></div>
                </a>
            @endforeach
        </div>
    </div>
@endsection