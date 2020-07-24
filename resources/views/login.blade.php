@extends('head')

@section('body')
    <div id="login-wrapper">
        <form id="login" action="{{ route('login_submit') }}" method="post">
            @csrf

            <h1>Login</h1>

            <div class="form-row">
                <input type="text" name="username" id="username" required>
                <label for="username">Username</label>
            </div>

            <div class="form-row">
                <input type="password" name="password" id="password" autocomplete="off" required>
                <label for="password">Password</label>
            </div>

            <button id="login-submit" type="submit">Login</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script
        src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    ></script>
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