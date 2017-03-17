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
            url: '/ajax-load-item',
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