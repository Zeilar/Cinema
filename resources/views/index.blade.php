<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Cinema</title>

        <!-- JavaScript -->
        <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"
        ></script>
        <script src="https://cdn.plyr.io/3.6.2/plyr.polyfilled.js"></script>
        <script src="https://kit.fontawesome.com/24d5fb23dd.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.plyr.io/3.6.2/plyr.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <div id="wrapper">
            <div id="theatre">
                <video id="videoWrapper" controls playsinline>
                    <source id="video" src="/storage/1.mp4">
                </video>

                <div id="chat">
                    <div id="chat-messages">
                        <div class="message">
                            <div class="message-avatar">
                                <img class="img-fluid" src="https://cdn.iconscout.com/icon/free/png-256/avatar-372-456324.png" alt="User avatar">
                            </div>
                            <div class="message-content">
                                A message, hello! It's a mildly long messageeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
                            </div>
                        </div>
                    </div>

                    <div id="chat-input">
                        <form action="/" method="post">
                            <input type="text" name="message" id="chat-send" autocomplete="off">
                            <button type="submit">Send</button>
                        </form>
                    </div>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#changeVideoModal">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="changeVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Change video</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="selection">
                                <form id="changeVideo">
                                    <input type="text" id="videoUrl" autocomplete="off">

                                    <select id="videoSelector">
                                        <option value="1.mp4">1</option>
                                        <option value="2.mp4">2</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- wrapper -->
        <script>
            const player = new Plyr('#videoWrapper');
        </script>
    </body>
</html>