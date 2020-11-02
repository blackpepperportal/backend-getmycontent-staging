var app = require('express')();
var fs = require('fs');
var debug = require('debug')('FANSCLUB:sockets');
var request = require('request');
var dotenv = require('dotenv').config();

var port = process.env.PORT || '3000';

var chat_save_url = process.env.APP_URL;

var SSL_KEY = process.env.SSL_KEY;

var SSL_CERTIFICATE = process.env.SSL_CERTIFICATE;

if( SSL_KEY && SSL_CERTIFICATE) {

    var https = require('https');

    var server = https.createServer({ 
                    key: fs.readFileSync(SSL_KEY),
                    cert: fs.readFileSync(SSL_CERTIFICATE) 
                 },app);


    server.listen(port);

} else {

    var server = require('http').Server(app);

    server.listen(port);   
}



var io = require('socket.io')(server);


io.on('connection', function (socket) {

    console.log('new connection established');

    socket.commonid = socket.handshake.query.commonid;

    console.log(socket.commonid);

    console.log(socket.handshake.query.commonid);
    
    socket.join(socket.handshake.query.commonid);

    socket.emit('connected', {'sessionID' : socket.handshake.query.commonid});

    socket.on('update sender', function(data) {

        console.log("Update Sender START");

        console.log('update sender', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        socket.emit('sender updated', 'Sender Updated ID:'+data.myid, 'Request ID:'+data.commonid);

        console.log("Update Sender END");

    });

    socket.on('message', function(data) {

        console.log("send message Start");

        var receiver = "user_id_"+data.user_id+"_to_user_id_"+data.to_user_id;

        console.log('data', data);

        console.log('receiver', receiver);

        var sent_status = socket.broadcast.to(receiver).emit('message', data);

        url = chat_save_url+'api/user/chat_messages/save?sender_user_id='+data.sender_user_id
        +'&to_user_id='+data.to_user_id
        +'&message='+data.message;

        console.log(url);

        request.get(url, function (error, response, body) {

        });

        console.log("send message END");

    });

    socket.on('disconnect', function(data) {

        console.log('disconnect', data);

    });
});