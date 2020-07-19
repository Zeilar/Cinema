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
                _token: csrfToken,
                video: Number(videoId),
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

    $('#changeVideo').submit(function(e) {
        e.preventDefault();
        console.log(e);
    });

    $('#youtubeUrl').keydown(function(e) {
       if (e.key === 'Enter') {
           console.log('submit youtube video');
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

    function addComment(comment) {
        const abbreviatedName = abbreviateName(comment.username);
        let message = $(`
            <div class="message">
                <div class="message-author" style="background-color: ${comment.color}; border-color: ${comment.color}" title="${comment.username}">
                    ${abbreviatedName}
                </div>
                <div class="message-content" style="background-color: ${comment.color}; border-color: ${comment.color}"></div>
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
            }
        });
    }, 1500));

    $('#videoWrapper').on('pause', _.throttle(function() {
        $.ajax({
            url: '/video/pause',
            method: 'POST',
            data: {
                _token: csrfToken,
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
                },
            });
        } else {
            $.ajax({
                url: '/chat/is_typing',
                method: 'POST',
                data: {
                    _token: csrfToken,
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
                    },
                });
            }
        }, 3000);
    }, 1500));

    $('#chat-submit').submit(_.throttle(function(e) {
        e.preventDefault();
        const chatInput = $('#chat-send');
        $.ajax({
            url: '/comment/send',
            method: 'POST',
            data: {
                _token: csrfToken,
                content: chatInput.val(),
            },
            success: function(data) {
                if (data.error) {
                    console.log(data.error);
                } else {
                    addComment(data.comment);
                }
            },
        });
        $.ajax({
            url: '/chat/is_not_typing',
            method: 'POST',
        });
        chatInput.val('').focus();
    }, 1500));

    $('#video-reset').click(_.throttle(function() {
        $.ajax({
            url: '/video/reset',
            method: 'POST',
            data: {
                _token: csrfToken,
            }
        });
    }, 1500));

    $('#video-sync').click(_.throttle(function() {
        $.ajax({
            url: '/video/sync',
            method: 'POST',
            data: {
                _token: csrfToken,
                timestamp: Number(player.currentTime),
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
        });

    Echo.channel('chat')
        .listen('NewComment', ({ comment }) => {
            addComment(comment);
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
        });

    Echo.channel('video')
        .listen('ChangeVideo', ({ video }) => {
            loadVideo(video);
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