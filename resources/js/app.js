import './bootstrap';

$(document).ready(function() {
    const plyr = new Plyr('#videoWrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const player = document.querySelector('#videoWrapper');
    const roomId = $('meta[name=roomId]').attr('content');
    let chatMessages = document.querySelector('#chat-messages');
    chatMessages.scrollTop = 99999;
    //player.volume = 0.5;

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
            success: function(data) {
                if (data.error) {
                    console.log(data.error);
                } else {
                    loadVideo(data.video);
                }
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
        const matches = name.match('([A-Z]+)');
        let abbreviatedName = '';
        matches.forEach(element => {
            abbreviatedName += element;
        });
        return abbreviatedName;
    }

    function addComment(comment, user) {
        const abbreviatedName = abbreviateName(user.username);
        let message = $(`
            <div class="message">
                <div class="message-author" style="background-color: ${user.color}; border-color: ${user.color}" title="${user.username}">
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
                _token: csrfToken,
                roomId: roomId,
            }
        });
    }, 1500));

    $('#videoWrapper').on('pause', _.throttle(function() {
        $.ajax({
            url: '/video/pause',
            method: 'POST',
            data: {
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

    $('#chat-submit').submit(_.throttle(function(e) {
        e.preventDefault();
        const chatInput = $('#chat-send');
        if ($('#chat-send').val() !== '') {
            $.ajax({
                url: '/comment/send',
                method: 'POST',
                data: {
                    content: chatInput.val(),
                    _token: csrfToken,
                    roomId: roomId,
                },
            });
            $.ajax({
                url: '/chat/is_not_typing',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    roomId: roomId,
                }
            });
            chatInput.val('').focus();
        }
    }, 1500));

    $('#video-reset').click(_.throttle(function() {
        $.ajax({
            url: '/video/reset',
            method: 'POST',
            data: {
                _token: csrfToken,
                roomId: roomId,
            }
        });
    }, 1500));

    $('#video-sync').click(_.throttle(function() {
        $.ajax({
            url: '/video/sync',
            method: 'POST',
            data: {
                timestamp: Number(player.currentTime),
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
                        class="online-user"
                        style="background-color: ${user.color}; border-color: ${user.color}" data-id="${user.id}"
                        title="${user.username}"
                    >
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            });
            chatMessages.scrollTop = 99999;
        })
        .joining(({ user }) => {
            if (!$(`.online-user[data-id=${user.id}]`).length) {
                $('#online-users').append(`
                    <div
                        class="online-user"
                        style="background-color: ${user.color}; border-color: ${user.color}" data-id="${user.id}"
                        title="${user.username}"
                    >
                        <span class="username">
                            ${abbreviateName(user.username)}
                        </span>
                    </div>
                `);
            }
            chatMessages.scrollTop = 99999;
        })
        .leaving(({ user }) => {
            $(`.online-user[data-id=${user.id}]`).remove();
            chatMessages.scrollTop = 99999;
        })
        .listen('NewComment', (data) => {
            addComment(data.comment, data.user);
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
        .listen('ConsoleLog', (data) => {
            console.log(data.user, data.message);
        })
        .listen('ChangeVideo', (data) => {
            switch (data.video.type) {
                case 'youtube': 
                    $('iframe').attr('src', data.video.url);
                    break;
            }
        })
        .listen('VideoPlay', () => {
            player.play();
        })
        .listen('VideoSync', ({ timestamp }) => {
            player.currentTime = timestamp;
        })
        .listen('VideoReset', () => {
            player.pause();
            player.currentTime = 0; 
        })
        .listen('VideoPause', () => {
            player.pause();
        });
});