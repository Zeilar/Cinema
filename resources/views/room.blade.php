@extends('head')

@section('body')
    <div id="wrapper">
        <div id="theatre">
            <div id="theatre-upper">
                <div id="playlist">
                    @if ($playlist = $room->playlist())
                        @foreach ($playlist as $i => $video)
                            <button class="playlist-button" type="button">
                                <div class="playlist-video" id="video-{{$i}}" data-id="{{ $video }}"></div>
                            </button>
                        @endforeach
                    @endif
                </div>
                <div id="player-wrapper">
                    <div class="input-group" id="add-video">
                        <div class="input-group-prepend">
                            <span class="input-group-text youtube-icon-wrapper">
                                <i class="fab fa-youtube mr-2"></i>
                                <span>Add to playlist</span>
                            </span>
                        </div>
                        <input type="text" id="youtubeUrl" autocomplete="off" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ" />
                    </div>
                    <div id="yt-player"></div>
                </div>

                <div id="chat">
                    <p class="room-name">{{ $room->name }}</p>

                    <div id="chat-messages">
                        @isset($room->comments)  
                            @foreach ($room->comments as $comment)
                                @php $user = $comment->user; @endphp
                                <div class="message" data-id="{{ $comment->id }}">
                                    <div class="message-timestamp">
                                        <span>{{ commentTimeFormat($comment->created_at) }}</span>
                                    </div>
                                    <div 
                                        class="message-author" title="{{ $user->username }}"
                                        style="background-color: {{ $user->color }}; border-color: {{ $user->color }}"
                                    >
                                        @if ($user->isRoomOwner($room))
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

                    <div id="online-users"><!-- Loaded by Echo through websocket --></div>

                    <div id="chat-input">
                        <div id="chat-submit">
                            <input type="text" id="chat-send" autocomplete="off" placeholder="Send a message" />
                            <button class="btn" id="chat-send-button" type="submit">Send</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="control-buttons">
                <button class="btn" id="video-sync" title="Sync with party">
                    <i class="fas fa-sync"></i>
                </button>

                <button class="btn" id="video-backward" title="Go back 15 seconds">
                    <i class="fas fa-backward"></i>
                </button>
                
                <button class="btn" id="video-play" title="Play/Resume">
                    <i class="fas fa-play"></i>
                </button>

                <button class="btn d-none" id="video-pause" title="Pause">
                    <i class="fas fa-pause"></i>
                </button>

                <button class="btn" id="video-forward" title="Go forward 15 seconds">
                    <i class="fas fa-forward"></i>
                </button>

                <button class="btn" id="video-reset" title="Reset">
                    <i class="fas fa-undo"></i>
                </button>

                <button class="btn" id="toggle-users" title="Show/hide online users">
                    <i class="fas fa-users"></i>
                </button>
            </div>
        </div>
    </div> <!-- wrapper -->
@endsection

@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection