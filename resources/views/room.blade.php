@extends('head')

@section('head')
    hejhe
@endsection

@section('body')
    <div id="wrapper">
        <div id="theatre">
            @if ($activeVideo)
                @if ($activeVideo->type === 'youtube')
                    <iframe 
                        id="yt-player" width="100%" allowfullscreen
                        src="https://www.youtube.com/embed/{{$activeVideo->path ?? 'dQw4w9WgXcQ'}}">
                    </iframe>
                @endif
                @if ($activeVideo->type === 'file')
                    <video id="videoWrapper" controls playsinline>
                        <source id="video" src="/storage/{{ $activeVideo->path ?? '1.mp4' }}">
                    </video> 
                @endif
            @else
                <div id="yt-player"></div>
            @endif

            <div id="chat">
                <p class="room-name">{{ $name }}</p>

                <div id="chat-messages">
                    @isset($comments)  
                        @foreach ($comments as $comment)
                            @php $user = $comment->user; @endphp
                            <div class="message" data-id="{{ $comment->id }}">
                                <div 
                                    class="message-author" title="{{ $user->username }}"
                                    style="background-color: {{ $user->color }}; border-color: {{ $user->color }}"
                                >
                                    @if ($user->isOwner($room))
                                        <img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />
                                    @endif
                                    <span>{{ abbreviateName($user->username) }}</span>
                                </div>
                                <div class="message-content" style="background: {{ $user->color }}">
                                    {{ $comment->content }}
                                </div>
                            </div>
                        @endforeach
                    @endisset
                </div>

                <div id="online-users">
                    <!-- Loaded by Echo through websocket -->
                </div>

                <div id="chat-input">
                    <form id="chat-submit">
                        <input type="text" id="chat-send" autocomplete="off" placeholder="Send a message" />
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
        <div class="modal fade" id="changeVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                    <input
                                        type="text" class="form-control" id="youtubeUrl" autocomplete="off"
                                        placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                                    />
                                </div>

                                @isset($videos)
                                    <select id="videoSelector">
                                        <!-- Local videos selection here -->
                                    </select>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- wrapper -->
@endsection

@section('scripts')
    <script src="https://cdn.plyr.io/3.6.2/plyr.polyfilled.js"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
    <script
        src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    ></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
@endsection