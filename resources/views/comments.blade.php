<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Cinema</title>

        <!-- JavaScript -->
        <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"
        ></script>
        <script src="https://cdn.plyr.io/3.6.2/plyr.polyfilled.js"></script>
        <script src="https://kit.fontawesome.com/24d5fb23dd.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.plyr.io/3.6.2/plyr.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

Comments

<input type="text" id="commentContent" autofocus>
<button id="commentSend">Send</button>

<div class="comments">
    @foreach ($comments as $comment)
        <div class="comment">
            <p>{!! $comment->content !!}</p>
        </div>
    @endforeach
</div>

@guest
    <h1>you are guest</h1>

    @php
        // If user has no account, create one for them and log them in automatically
        $user = \App\User::create([
            'username' => new \Nubs\RandomNameGenerator\Alliteration(),
            'role' => 'viewer',
        ]);
        Auth::login($user);
    @endphp
@endguest