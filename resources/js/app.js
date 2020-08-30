import './bootstrap';

/*
$(document).ready(() => {
    const activeVideo = $('meta[name="activeVideo"]').attr('content');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let readyUsers = [];
    let users = [];
    let player;
    $.ajax({
        url: '/user/info',
        method: 'POST',
        data: {
            _token: csrfToken,
        },
        success: user => {
            sessionStorage.setItem('user', JSON.stringify(user));
        },
    });
    const roomId = $('meta[name=roomId]').attr('content');
    const chatMessages = document.querySelector('#chat-messages');

    chatMessages.scrollTop = 99999;

    function getUser() {
        return JSON.parse(sessionStorage.getItem('user')) ?? false;
    }

    $('#youtubeUrl').keyup(function(e) {
        if (e.key === 'Enter') {
            let url = '';

            try {
                url = new URL($(this).val());
            } catch (e) {
                return alert('Invalid URL, please try again\nMake sure it contains "v="');
            }

            const parameter = new URLSearchParams(url.search);
            const videoId = parameter.get('v');

            if (videoId) {
                $.ajax({
                    url: '/video/add',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        videoId: videoId,
                        roomId: roomId,
                    },
                });
                $(this).val('');
                notification('Added video to playlist', null, 'fa fa-plus');
            } else {
                alert('Invalid URL, please try again\nMake sure it contains "v="');
            }
        }
    });

    function abbreviateName(name) {
        const regex = RegExp('[A-Z]+', 'g');
        const matches = [...name.matchAll(regex)];
        let abbreviatedName = '';
        matches.forEach(element => abbreviatedName += element[0]);
        return abbreviatedName;
    }

    function addComment(comment, user) {
        if (!comment.timestamp) {
            const date = new Date();
            let minutes = date.getMinutes();
            let hours = date.getHours();
            if (minutes < 10) minutes = `0${date.getMinutes()}`;
            if (hours < 10) hours = `0${date.getHours()}`;
            comment.timestamp = `${hours}:${minutes}`;
        }
        const message = $(`
            <div class="message" data-id="${comment.id}">
                <div class="message-timestamp">
                    <span>${comment.timestamp}</span>
                </div>
                <div class="message-author" style="background-color: ${user.color}; border-color: ${user.color}" title="${user.username}">
                    ${user.isRoomOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                    ${abbreviateName(user.username)}
                </div>
                <div class="message-content" style="background-color: ${user.color}; border-color: ${user.color}"></div>
            </div>
        `);

        // Do this in order to escape tags and other unwanted characters in the message body
        message.find('.message-content').text(comment.content);
        $('#chat-messages').append(message);

        chatMessages.scrollTop = 99999;
    }

    $('#toggle-users').click(function() {
        $('#online-users').toggleClass('d-none');
    });

    $('#chat-send').on('input', _.throttle(function() {
        if ($(this).val() === '') {
            $.ajax({
                url: '/chat/is_not_typing',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    roomId: roomId,
                },
            });
        } else {
            $.ajax({
                url: '/chat/is_typing',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    roomId: roomId,
                },
            });
        }

        setTimeout(() => {
            if ($(this).val() === '') {
                $.ajax({
                    url: '/chat/is_not_typing',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            }
        }, 3000);
    }, 1500));

    $('#chat-send-button').click(() => {
        submitComment();
    });

    $('#chat-send').keydown(function(e) {
        if (e.key === 'Enter') submitComment();
    });

    function submitComment() {
        const chatInput = $('#chat-send');
        const value = chatInput.val();
        chatInput.val('').focus();
        if (value !== '') {
            $.ajax({
                url: '/comment/send',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    content: value,
                    roomId: roomId,
                },
            });
        }
        $.ajax({
            url: '/chat/is_not_typing',
            method: 'POST',
            data: {
                _token: csrfToken,
                roomId: roomId,
            }
        });
    }

    function notification(message = '', user, type = '') {
        $('.notification').remove();
        let username = '';
        let color = 'black';
        if (user) {
            color = user.color ?? 'black';
            username = `<span class="notification-username" style="background: ${color};">${user.username}</span>`;
        }
        const notification = $(`
            <div class="notification" style="box-shadow: 0 0 3px 0 ${color};">
                <div class="notification-icon">
                    <i class="${type ?? ''}"></i>
                </div>
                <div class="notification-message">
                    ${username}
                    <span class="notification-content">${message}</span>
                </div>
            </div>
        `);
        $('body').append(notification);

        setTimeout(() => {
            notification.remove();
        }, 4000);
    }

    $('#video-reset').click(function() {
        $.ajax({
            url: '/video/reset',
            method: 'POST',
            data: {
                type: $(this).find('i').attr('class'),
                _token: csrfToken,
                roomId: roomId,
            },
        });
    });

    Echo.join(`room-${roomId}`)
        .here(data => {
            $('#users-loading').remove();
            data.forEach(({ user }) => {
                users.push(user.id);
                $('#online-users').append(`
                    <div
                        class="online-user" title="${user.username}" data-id="${user.id}"
                        style="background-color: ${user.color}; border-color: ${user.color}"
                    >
                        ${user.isRoomOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            });
            chatMessages.scrollTop = 99999;
        })
        .joining(({ user }) => {
            users.push(user.id);
            addComment({content: 'has joined the chat'}, user);
            if (!$(`.online-user[data-id=${user.id}]`).length) {
                $('#online-users').append(`
                   <div
                        class="online-user" title="${user.username}" data-id="${user.id}"
                        style="background-color: ${user.color}; border-color: ${user.color}"
                    >
                        ${user.isRoomOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            }
            chatMessages.scrollTop = 99999;
        })
        .leaving(({ user }) => {
            const index = users.indexOf(user.id);
            if (index !== -1) users.splice(index, 1);
            addComment({content: 'has left the chat'}, user);
            $(`.online-user[data-id=${user.id}]`).remove();
            chatMessages.scrollTop = 99999;
        })
        .listen('NewComment', data => {
            addComment(data.comment, data.user);
        })
        .listen('DeleteComment', data => {
            $(`.message[data-id=${data.id}]`).remove();
        })
        .listen('IsTyping', ({ user }) => {
            user = $(`.online-user[title="${user.username}"]`);
            const dots = $(`
                <span class="dots">
                    <span>.</span>
                    <span>.</span>
                    <span>.</span>
                </span>
            `);
            if (!user.find('.dots').length) user.append(dots);
            setTimeout(() => {
                dots.remove();
            }, 10000);
        })
        .listen('IsNotTyping', ({ user }) => {
            $(`.online-user[title="${user.username}"]`).find('.dots').remove();
        })
        .listen('Notification', data => {
            notification(data.message, data.user, data.type);
        })
        .listen('AddVideo', data => {
            const player = $(`
                <button class="playlist-button" type="button">
                    <img class="img-fluid" src="https://img.youtube.com/vi/${data.videoId}/0.jpg" alt="YouTube video thumbnail">
                </button>
            `);
            $('#playlist .playlist-videos').append(player);
        })
        .listen('VideoPlay', () => {
            playVideo();
        })
        .listen('VideoTime', ({ timestamp }) => {
            changeVideoTime(timestamp);
        })
        .listen('VideoReset', () => {
            changeVideoTime(0);
        })
        .listen('VideoPause', () => {
            pauseVideo();
        })
        .listen('WatchedVideo', ({ user }) => {
            if (!readyUsers.find(element => element === user.id)) readyUsers.push(user.id);
        });

    function playVideo() {
        player.playVideo();
        $('#video-pause').removeClass('d-none');
        $('#video-play').addClass('d-none');
    }

    function pauseVideo() {
        player.pauseVideo();
        $('#video-play').removeClass('d-none');
        $('#video-pause').addClass('d-none');
    }

    function changeVideoTime(timestamp) {
        player.seekTo(timestamp);
        playVideo();
        pauseVideo();
    }

    window.YT.ready(() => {
        player = new YT.Player('yt-player', {
            videoId: activeVideo ?? 'dQw4w9WgXcQ',
            events: {
                onStateChange: watchedVideo,
                onReady: initHandlers,
            },
            playerVars: {
                iv_load_policy: 3,
                autoplay: 1,
                fs: 0,
            }
        });

        function watchedVideo(e) {
            if (e.data === YT.PlayerState.ENDED) {
                $.ajax({
                    url: '/video/watched',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            }
        }

        function initHandlers() {
            $('#video-play').click(function() {
                $.ajax({
                    url: '/video/play',
                    method: 'POST',
                    data: {
                        type: $(this).find('i').attr('class'),
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            });

            $('#video-pause').click(function() {
                $.ajax({
                    url: '/video/pause',
                    method: 'POST',
                    data: {
                        type: $(this).find('i').attr('class'),
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            });

            $('#video-sync').click(function() {
                $.ajax({
                    url: '/video/change_time',
                    method: 'POST',
                    data: {
                        type: $(this).find('i').attr('class'),
                        timestamp: player.getCurrentTime(),
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            });

            $('#video-forward').click(function() {
                $.ajax({
                    url: '/video/change_time',
                    method: 'POST',
                    data: {
                        timestamp: player.getCurrentTime() + 15,
                        type: $(this).find('i').attr('class'),
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            });

            $('#video-backward').click(function() {
                $.ajax({
                    url: '/video/change_time',
                    method: 'POST',
                    data: {
                        timestamp: player.getCurrentTime() - 15,
                        type: $(this).find('i').attr('class'),
                        _token: csrfToken,
                        roomId: roomId,
                    },
                });
            });
        }
    });
});
*/

