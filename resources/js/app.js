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

        ajax_csrf();
        $.ajax({
            url: '/video/change',
            method: 'POST',
            data: {
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

    function ajax_csrf() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });
    }

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
                <div class="message-author ${comment.color}" title="${comment.username}">
                    ${abbreviatedName}
                </div>
                <div class="message-content ${comment.color}">
                    
                </div>
            </div>
        `);

        // Do this in order to escape tags and other unwanted characters in the message body
        message.find('.message-content').text(comment.content);
        $('#chat-messages').append(message);

        chatMessages.scrollTop = 99999;
    }

    $('#videoWrapper').on('play', function() {
        ajax_csrf();
        $.ajax({
            url: '/video/play',
            method: 'POST',
        });
    });

    $('#videoWrapper').on('pause', function() {
        ajax_csrf();
        $.ajax({
            url: '/video/pause',
            method: 'POST',
        });
    });

    $('#chat-submit').submit(function(e) {
        e.preventDefault();
        const chatInput = $('#chat-send');
        ajax_csrf();
        $.ajax({
            url: '/comment/send',
            method: 'POST',
            data: {
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
        chatInput.val('').focus();
    });

    $('#video-reset').click(function() {
        ajax_csrf();
        $.ajax({
            url: '/video/reset',
            method: 'POST',
        });
    });

    $('#video-sync').click(function() {
        ajax_csrf();
        $.ajax({
            url: '/video/sync',
            method: 'POST',
            data: {
                timestamp: Number(player.currentTime),
            },
        });
    });

    setInterval(() => {
    }, 1000);
    Echo.channel('chat').
        listen('NewComment', (data) => {
            addComment(data.comment);
        });

    Echo.channel('video')
        .listen('ChangeVideo', (data) => {
            loadVideo(data.video);
        })
        .listen('VideoPlay', () => {
            player.play();
        })
        .listen('VideoSync', (data) => {
            player.currentTime = data.timestamp;
        })
        .listen('VideoReset', () => {
            player.pause();
            player.currentTime = 0; 
        })
        .listen('VideoPause', () => {
            player.pause();
        });
});