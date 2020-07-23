<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Setup -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
        <title>Cinema</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.plyr.io/3.6.2/plyr.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        @isset($id)
            <meta name="roomUuid" content="{{ $id }}">
        @endisset
    </head>
    <body>
        @yield('body')
        @yield('footer')
    </body>

    <!-- JavaScript -->
    @yield('scripts')
    <script src="https://kit.fontawesome.com/24d5fb23dd.js" crossorigin="anonymous"></script>
</html>