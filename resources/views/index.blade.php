<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body>
        <div id="wrapper">
            <div id="theatre">
                @php $video = \App\Video::find(Illuminate\Support\Facades\Cache::get('activeVideo')) @endphp
                @isset ($video->embed)
                    {!! $video->embed !!}
                @else
                    <video id="videoWrapper" controls playsinline>
                        <source id="video" src="/storage/{{ $video->path ?? '1.mp4' }}">
                    </video>
                @endif

                <div id="chat">
                    <div id="chat-users">
                        @foreach ($online_users as $user)
                            <div class="online-user">

                            </div>
                        @endforeach
                    </div>

                    <div id="chat-messages">
                        @foreach ($comments as $comment)
                            @php $user = $comment->user; @endphp
                            <div class="message">
                                <div class="message-author {{ $user->color }}" title="{{ $user->username }}">
                                    @php $abbrevatedName = ''; @endphp
                                    @php preg_match_all('([A-Z]+)', $user->username, $matches); @endphp
                                    @foreach ($matches[0] as $letter)
                                        @php $abbrevatedName .= $letter; @endphp
                                    @endforeach
                                    {{ $abbrevatedName }}
                                </div>
                                <div class="message-content {{ $user->color }}">
                                    {{ $comment->content }}
                                </div>  
                            </div>
                        @endforeach
                    </div>

                    <div id="chat-input">
                        <form id="chat-submit">
                            <input type="text" id="chat-send" autocomplete="off" placeholder="Send a message" required />
                            <button class="btn" id="chat-send-button" type="submit">Send</button>
                        </form>

                        <div id="chat-controls">
                            <button type="button" class="btn btn-primary" title="Change video" data-toggle="modal" data-target="#changeVideoModal">
                                <i class="fas fa-exchange-alt"></i>
                            </button>

                            <button class="btn" id="video-sync" data-placement="top" title="Sync with party">
                                <i class="fas fa-sync"></i>
                            </button>
                            
                            <button class="btn" id="video-reset" title="Reset">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="changeVideoModal" title="Change video" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Change video</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="selection">
                                <form id="changeVideo">
                                    <input type="text" id="videoUrl" autocomplete="off">

                                    <select id="videoSelector">
                                        @foreach ($videos as $video)
                                            <option value="{{ $video->id }}">{{ $video->title }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- wrapper -->
        <script>
            const player = new Plyr('#videoWrapper');
        </script>

        @if ($broadcast)
            <script>
                $.ajax({
                    url: '{{ route("comment_send") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ Session::token() }}',
                        content: 'has joined the chat',
                    },
                });
            </script>
        @endif
    </body>
</html>