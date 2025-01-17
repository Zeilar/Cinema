@extends('head')

@section('body')
    <div id="login-wrapper">
        <form id="login" action="{{ route('login_submit') }}" method="post">
            @csrf

            <div class="login-banner">
                <img src="/storage/icons/camera.svg" alt="Site icon" class="img-fluid">
            </div>

            @if (session('error'))
                <p class="form-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </p>
            @endif

            <div class="form-row @error('username') error @enderror">
                @error('username')
                    <p class="field-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <input type="text" name="username" id="username" value="{{ old('username') ?? '' }}">
                <label @if(old('username')) class="stay" @endif for="username">Username</label>
            </div>

            <div class="form-row @error('password') error @enderror">
                @error('password')
                    <p class="field-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <input type="password" name="password" id="password" autocomplete="off">
                <label for="password">Password</label>
            </div>

            <button id="login-submit" type="submit">Login</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $('#login').submit(function() {
            $('#login-submit').attr('disabled', true); 
        });
        
        $('input').blur(function() {
            const label = $(this).siblings('label');
            $(this).val() !== '' ? label.addClass('stay') : label.removeClass('stay');
        });
    </script>
@endsection