var port = 3009;

var app = require('http').createServer().listen(port);
var io = require('socket.io')(app);



io.sockets.on('connection', function (socket){

    socket.on('addedArticle', function (data) {
        io.emit('newArticles', data.count);
    });


    socket.on('disconnect', function () {
        console.log('Socket.io is Disconect');
    });
});
