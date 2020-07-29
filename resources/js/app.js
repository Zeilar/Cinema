import './bootstrap';

$(document).ready(() => {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/user/info',
        method: 'POST',
        data: {
            _token: csrfToken,
        },
        success: function(user) {
            sessionStorage.setItem('user', JSON.stringify(user));
        },
    });
    const plyr = new Plyr('#videoWrapper');
    const player = document.querySelector('#videoWrapper');
    const roomId = $('meta[name=roomId]').attr('content');
    const chatMessages = document.querySelector('#chat-messages');

    chatMessages.scrollTop = 99999;
    //player.volume = 0.5;

    function getUser() {
        return JSON.parse(sessionStorage.getItem('user')) ?? false;
    }

    $('#videoSelector').change(function() {
        const index = $(this)[0].selectedIndex;
        const videoId = $(this).children()[index].getAttribute('value');

        $.ajax({
            url: '/video/change',
            method: 'POST',
            data: {
                video: Number(videoId),
                _token: csrfToken,
                roomId: roomId,
            },
        });
    });

    $('#youtubeUrl').keyup(function(e) {
        let url = $(this).val();
        if (url.includes('https://www.youtube.com/watch?v=') && e.key === 'Enter') {
            url = url.replace('watch?v=', 'embed/');
            $.ajax({
                url: '/video/change',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    type: 'youtube',
                    roomId: roomId,
                    url: url,
                },
                success: () => {
                    $('#changeVideoModal').modal('hide');
                    $(this).val('');
                }
            });
        }
    });

    function abbreviateName(name) {
        const regex = RegExp('[A-Z]+', 'g');
        const matches = [...name.matchAll(regex)];
        let abbreviatedName = '';
        matches.forEach(element => {
            abbreviatedName += element[0];
        });
        return abbreviatedName;
    }

    function addComment(comment, user) {
        const abbreviatedName = abbreviateName(user.username);
        const message = $(`
            <div class="message" data-id="${comment.id}">
                <div class="message-timestamp">
                    <span>${comment.timestamp}</span>
                </div>
                <div class="message-author" style="background-color: ${user.color}; border-color: ${user.color}" title="${user.username}">
                    ${user.isOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                    ${abbreviatedName}
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

    $('#videoWrapper').on('play', _.throttle(function() {
        $.ajax({
            url: '/video/play',
            method: 'POST',
            data: {
                type: $(this).find('i').attr('class'),
                roomId: roomId,
                _token: csrfToken,
            }
        });
    }, 1500));

    $('#videoWrapper').on('pause', _.throttle(function() {
        $.ajax({
            url: '/video/pause',
            method: 'POST',
            data: {
                type: $(this).find('i').attr('class'),
                _token: csrfToken,
                roomId: roomId,
            }
        });
    }, 1500));

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

    $('#chat-send-button').click(function() {
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

    function notification(message, user, type) {
        $('.notification').remove();
        const notification = $(`
            <div class="notification" style="box-shadow: 0 0 5px 1px ${user.color};">
                <div class="notification-icon">
                    <i class="${type}"></i>
                </div>
                <div class="notification-message">
                    <span class="username" style="background: ${user.color};">${user.username}</span>
                    <span class="message">${message}</span>
                </div>
            </div>
        `);
        $('body').append(notification);

        setTimeout(() => {
            notification.remove();
        }, 4000);
    }

    $('#video-reset').click(_.throttle(function() {
        $.ajax({
            url: '/video/reset',
            method: 'POST',
            data: {
                type: $(this).find('i').attr('class'),
                _token: csrfToken,
                roomId: roomId,
            },
        });
    }, 1500));

    $('#video-sync').click(_.throttle(function() {
        $.ajax({
            url: '/video/sync',
            method: 'POST',
            data: {
                //timestamp: Number(player.currentTime),
                type: $(this).find('i').attr('class'),
                _token: csrfToken,
                roomId: roomId,
            },
        });
    }, 1500));

    Echo.join(`room-${roomId}`)
        .here((data) => {
            data.forEach(({ user }) => {
                $('#online-users').append(`
                    <div
                        class="online-user" title="${user.username}" data-id="${user.id}"
                        style="background-color: ${user.color}; border-color: ${user.color}"
                    >
                        ${user.isOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            });
            chatMessages.scrollTop = 99999;
        })
        .joining(({ user }) => {
            addComment({content: 'has joined the chat'}, user);
            if (!$(`.online-user[data-id=${user.id}]`).length) {
                $('#online-users').append(`
                   <div
                        class="online-user" title="${user.username}" data-id="${user.id}"
                        style="background-color: ${user.color}; border-color: ${user.color}"
                    >
                        ${user.isOwner ? '<img class="img-fluid user-crown" src="/storage/icons/crown.svg" alt="Crown" title="Room owner" />' : ''}
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            }
            chatMessages.scrollTop = 99999;
        })
        .leaving(({ user }) => {
            addComment({content: 'has left the chat'}, user);
            $(`.online-user[data-id=${user.id}]`).remove();
            chatMessages.scrollTop = 99999;
        })
        .listen('NewComment', (data) => {
            addComment(data.comment, data.user);
        })
        .listen('DeleteComment', (data) => {
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
        .listen('Notification', (data) => {
            notification(data.message, data.user, data.type);
        })
        .listen('ChangeVideo', (data) => {
            switch (data.video.type) {
                case 'youtube': 
                    $('iframe').attr('src', data.video.url);
                    break;
            }
        })
        .listen('VideoPlay', () => {
            //player.play();
        })
        .listen('VideoSync', ({ timestamp }) => {
            //player.currentTime = timestamp;
        })
        .listen('VideoReset', () => {
            //player.pause();
            //player.currentTime = 0; 
        })
        .listen('VideoPause', () => {
            //player.pause();
        });

    $('.comment-remove').click(function() {
        $.ajax({
            url: '/comment/delete',
            method: 'POST',
            data: {
                id: $(this).parents('.message').attr('data-id'),
                _token: csrfToken,
                roomId: roomId,
            },
        }); 
    });

    function onPlayerReady() {
        console.log('YT player is ready');
    }

    window.YT.ready(function() {
        const ytPlayer = new YT.Player('yt-player', {
            videoId: 'M7lc1UVf-VE', // change to something else
            events: {
                onReady: onPlayerReady,
            },
            playerVars: {
                'origin': 'http://cinema.test', // remove this in production
            }
        });
    });
});