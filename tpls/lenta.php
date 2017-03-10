<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>RSS reader</title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/yeti/bootstrap.min.css" rel="stylesheet" integrity="sha384-HzUaiJdCTIY/RL2vDPRGdEQHHahjzwoJJzGUkYjHVzTwXFQ2QN/nVgX7tzoMW3Ov" crossorigin="anonymous">

    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <script src="/assets/socket.io.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">RSS</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">


            </ul>


        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
<div class="new-items animated fadeInLeft">
    <div class="close close-new-items">X</div>
    <div id="amount-items"></div>
    Новых записей!
    <button class="btn btn-primary btn-block btn-xs" id="load-new">Подгрузить новые</button>
</div>
    <div class="row">
        <div class="grid">
            <? include 'items.php'?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4 col-md-offset-4">

        </div>
    </div>

</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://unpkg.com/imagesloaded@4.1/imagesloaded.pkgd.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<script>

    $(document).ready(function(){
        var socket = io('http://rss.iused.top');

        // Новые записи
        var newItems = 0;

        socket.on('connect',function() {
            socket.emit('room', 'rss');
            console.log('Client has connected to the server!');
        });



        socket.on('disconnect', function() {
            console.log("Socket disconnected.");


        });

        socket.on('newArticles', function (data) {
            $('.new-items').fadeIn();
            newItems += parseInt(data);
            $('#amount-items').text(newItems);
            console.log(newItems)
        })

        var $grid = $('.grid').masonry({
            // options
            itemSelector: '.item-grid',

        });

        $grid.imagesLoaded().progress( function() {
            $grid.masonry('layout');
        });

        $('.close-new-items').click(function () {
            $(this).parent('.new-items').hide();
        });


        $('#load-new').on('click' , function (event) {
            _self = $(this);
            $.ajax({
                method: 'post',
                url: '/getNewItems.php',
                data: {getNew : newItems },
                beforeSend: function () {
                    _self.prop('disabled' , true);
                },
                success: function (data , event) {
                    if(data != 'notFound'){
                        newItems = 0;
                        $('.new-items').hide();
                        $('#amount-items').text(newItems);
                        $('.grid').prepend(data);
                        $grid.imagesLoaded().progress( function() {
                            $grid.masonry('reloadItems');
                            $grid.masonry('layout');
                        })

                    }
                },
                complete: function (data) {
                    _self.prop('disabled' , false);
                }
            });

        });

    });
    
    








</script>

</body>
</html>