import './bootstrap';

$(document).ready(function() {
    const originalHTML = $('.plyr');
    const video = document.querySelector('#videoWrapper');
    let chatMessages = document.querySelector('#chat-messages');
    chatMessages.scrollTop = 99999;
    video.volume = 0.5;

    $('#videoSelector').change(function() {
        $('iframe').replaceWith(originalHTML);
        video.pause();
        const index = $(this)[0].selectedIndex;
        const videoPath = $(this).children()[index].getAttribute('value');
        $('#video').attr('src', `storage/${videoPath}`);
        video.load();
    });

    $('#changeVideo').submit(function(e) {
        e.preventDefault();
        $('iframe').replaceWith(originalHTML);

        if ($('#videoUrl').val().search('</iframe>') !== -1) {
            console.log('is iframe');
            video.remove();
            $('.plyr').replaceWith($('#videoUrl').val());
            return;
        }

        video.pause();
        $('#video').attr('src', $('#videoUrl').val());
        video.load();
    });

    function addComment(comment) {
        const matches = comment.username.match('([A-Z]+)');
        let abbrevatedName = '';
        matches.forEach(element => {
            abbrevatedName += element;
        });
        $('#chat-messages').append(`
            <div class="message">
                <div class="message-author ${comment.color}">
                    ${abbrevatedName}
                </div>
                <div class="message-content ${comment.color}">
                    ${comment.content}
                </div>
            </div>
        `);
        chatMessages.scrollTop = 99999;
    }

    $('#chat-submit').submit(function(e) {
        const chatInput = $('#chat-send');
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });
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
                    chatInput.val('').focus();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
        chatInput.val('');
    });

    Echo.channel('comments').listen('NewComment', (data) => {
        addComment(data.comment);
    });
});