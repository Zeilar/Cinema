@extends('head')

@section('body')
    <div id="wrapper">
        <div id="theatre">
            <div id="theatre-left">
                <div id="yt-player"></div>
                <div id="controls">
                    <button type="button" class="btn" title="Change video" data-toggle="modal" data-target="#changeVideoModal">
                        <i class="fas fa-exchange-alt"></i>
                    </button>

                    <button class="btn" id="video-sync" title="Sync with party">
                        <i class="fas fa-sync"></i>
                    </button>

                    <button class="btn" id="video-back" title="Go back 15 seconds">
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
                <div id="playlist">
                    Playlist
                </div>
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

                <div id="online-users">
                    <!-- Loaded by Echo through websocket -->
                </div>

                <div id="chat-input">
                    <div id="chat-submit">
                        <input type="text" id="chat-send" autocomplete="off" placeholder="Send a message" />
                        <button class="btn" id="chat-send-button" type="submit">Send</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="changeVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add video</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="selection">
                            <form id="changeVideo">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text youtube-icon-wrapper">
                                            <i class="fab fa-youtube"></i>
                                        </span>
                                    </div>
                                    <input
                                        type="text" id="youtubeUrl" autocomplete="off"
                                        placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- theatre -->
    </div> <!-- wrapper -->
@endsection

@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection