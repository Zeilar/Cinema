@extends('head')

@section('body')
    <div class="wrapper-index">
        <form class="new-room" action="{{ route('room_create') }}" method="post">
            @csrf
            <button class="btn room-create" type="submit">Create a new room</button>
        </form>

        <div class="existing-rooms">
            @foreach ($rooms as $room)
                <a class="btn room-join" href="{{ route('room_enter', $room->anonymous_id) }}">
                    <span>Enter</span>
                    <div class="circle"></div>
                </a>
            @endforeach
        </div>
    </div>
@endsection