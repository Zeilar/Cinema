import './bootstrap';

$(document).ready(function() {
    const originalHTML = $('.plyr');
    const video = document.querySelector('#videoWrapper');
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
});