<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Setup and variables -->
        @isset($room)
            <meta name="activeVideo" content="{{ $room->activeVideo() }}">
            <meta name="roomId" content="{{ $room->id }}">
        @endisset
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <title>Cinema</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- JS -->
        <script
            src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        ></script>
        <script src="https://www.youtube.com/iframe_api"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        @empty($hideNavbar)
            @include('navbar')
        @endempty
        @yield('body')
        @yield('footer')
    </body>

    <!-- JavaScript -->
    <script src="https://kit.fontawesome.com/24d5fb23dd.js" crossorigin="anonymous"></script>
    @yield('scripts')
</html>