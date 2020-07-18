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
                @isset($activeVideo)
                    @isset($activeVideo->embed)
                        @php 
                            $activeVideo = str_replace(
                                [
                                    'https://www.youtube.com/watch?v=',
                                    'youtu.be'
                                ],
                                '',
                                $activeVideo->embed
                            );
                        @endphp
                        <iframe width="100%" src="https://www.youtube.com/embed/{{$activeVideo}}" frameborder="0"; allowfullscreen></iframe>
                    @endisset
                    @isset($activeVideo->path)
                        <video id="videoWrapper" controls playsinline>
                            <source id="video" src="/storage/{{ $activeVideo->path ?? '1.mp4' }}">
                        </video> 
                    @endisset
                @else
                    <iframe width="100%" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0"; allowfullscreen></iframe>
                @endisset

                <div id="chat">
                    <div id="chat-messages">
                        @foreach ($comments as $comment)
                            @php $user = $comment->user; @endphp
                            <div class="message">
                                <div 
                                    class="message-author" 
                                    style="background-color: {{ $user->color }}; border-color: {{ $user->color }}"
                                    title="{{ $user->username }}"
                                >
                                    {{ abbreviateName($user->username) }}
                                </div>
                                <div class="message-content" style="background: {{ $user->color }}">
                                    {{ $comment->content }}
                                </div>  
                            </div>
                        @endforeach
                    </div>

                    <div id="online-users">
                        <!-- Loaded by Echo through websocket -->
                    </div>

                    <div id="chat-input">
                        <form id="chat-submit">
                            <input type="text" id="chat-send" autocomplete="off" placeholder="Send a message" required />
                            <button class="btn" id="chat-send-button" type="submit">Send</button>
                        </form>

                        <div id="chat-controls">
                            <button type="button" class="btn" title="Change video" data-toggle="modal" data-target="#changeVideoModal">
                                <i class="fas fa-exchange-alt"></i>
                            </button>

                            <button class="btn" id="video-sync" data-placement="top" title="Sync with party">
                                <i class="fas fa-sync"></i>
                            </button>
                            
                            <button class="btn" id="video-reset" title="Reset">
                                <i class="fas fa-undo"></i>
                            </button>

                            <button class="btn" id="toggle-users" title="Show/hide online users">
                                <i class="fas fa-users"></i>
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

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <span>YouTube</span>
                                                <i class="fab ml-2 fa-youtube"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="URL" id="youtubeUrl">
                                    </div>

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
    </body>
</html>