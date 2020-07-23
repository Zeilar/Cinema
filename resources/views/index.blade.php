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
            <button class="btn room-create" type="submit">Create a new room</button>
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