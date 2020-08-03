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

            @if (session('error'))
                <p class="form-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </p>
            @endif

            <div class="form-row @error('roomName') error @enderror">
                @error('roomName')
                    <p class="field-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <input type="text" name="roomName" id="room-name-input" autocomplete="off" value="{{ old('roomName') ?? '' }}" />
                <label @if(old('roomName')) class="stay" @endif for="room-name-input">Room name</label>
            </div>
            <button class="btn room-create" type="submit" style="background: {{ $user->color }};">
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

@section('scripts')
    <script>
        $('.new-room').submit(function() {
            $('.room-create').attr('disabled', true); 
        });
        
        $('input').blur(function() {
            const label = $(this).siblings('label');
            $(this).val() !== '' ? label.addClass('stay') : label.removeClass('stay');
        });
    </script>
@endsection