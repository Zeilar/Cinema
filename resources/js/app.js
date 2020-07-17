import './bootstrap';

$(document).ready(function() {
    const originalHTML = $('.plyr');
    const player = document.querySelector('#videoWrapper');
    let chatMessages = document.querySelector('#chat-messages');
    chatMessages.scrollTop = 99999;
    player.volume = 0.5;

    function loadVideo(video) {
        if (video.path) {
            $('iframe').replaceWith(originalHTML);
            player.pause();
            $('#video').attr('src', `storage/${video.path}`);
            player.load();
        } else if (video.embed) {
            if ($('#videoUrl').val().search('</iframe>') !== -1) {
                // player.remove();
                // $('.plyr').replaceWith($('#videoUrl').val());
            }
        }
    }

    $('#videoSelector').change(function() {
        const index = $(this)[0].selectedIndex;
        const videoId = $(this).children()[index].getAttribute('value');

        $.ajax({
            url: '/video/change',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
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
        //
    });

    function abbreviateName(name) {
        const matches = name.match('([A-Z]+)');
        let abbrevatedName = '';
        matches.forEach(element => {
            abbrevatedName += element;
        });
        return abbrevatedName;
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

    $('#videoWrapper').on('play', function() {
        $.ajax({
            url: '/video/play',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            }
        });
    });

    $('#videoWrapper').on('pause', function() {
        $.ajax({
            url: '/video/pause',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            }
        });
    });

    $('#chat-send').on('input', function() {
        if ($(this).val() === '') {
            $.ajax({
                url: '/chat/is_not_typing',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
            });
        } else {
            $.ajax({
                url: '/chat/is_typing',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
            });
        }

        setTimeout(() => {
            if ($(this).val() === '') {
                $.ajax({
                    url: '/chat/is_not_typing',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                });
            }
        }, 3000);
    });

    $('#chat-submit').submit(function(e) {
        e.preventDefault();
        const chatInput = $('#chat-send');
        $.ajax({
            url: '/comment/send',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
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
    });

    $('#video-reset').click(function() {
        $.ajax({
            url: '/video/reset',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            }
        });
    });

    $('#video-sync').click(function() {
        $.ajax({
            url: '/video/sync',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                timestamp: Number(player.currentTime),
            },
        });
    });

    Echo.join('party')
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
            if (!user.find('.dots').length) {
                user.append(dots);
            }
            setTimeout(() => {
                dots.remove();
            }, 5000);
        })
        .listen('IsNotTyping', ({ user }) => {
            $(`.online-user[title="${user.username}"]`).find('.dots').remove();
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