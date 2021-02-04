var app = require('express')();
var fs = require('fs');
var debug = require('debug')('FANSCLUB:sockets');
var request = require('request');
var dotenv = require('dotenv').config();

var port = process.env.PORT || '3012';

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

    socket.on('notification update', function(data) {

        console.log("notification update START");

        console.log('notification update', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        setInterval(function (){
            
            var notification_receiver = "user_id_"+data.myid;

            console.log('receiver', notification_receiver);

            var data ={user_id:data.myid};

            var notification_data = {chat_notification:1, bell_notification:1};

            var notification_status = socket.broadcast.to(notification_receiver).emit('notification', notification_data);

        }, 60000);

    });

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

        console.log("ON message", data);

        if(data.loggedin_user_id == data.from_user_id) {

            var receiver = "user_id_"+data.to_user_id+"_to_user_id_"+data.from_user_id;

        } else {

            var receiver = "user_id_"+data.from_user_id+"_to_user_id_"+data.to_user_id;
        }


        console.log('data', data);

        console.log('receiver', receiver);

        var sent_status = socket.broadcast.to(receiver).emit('message', data);

        url = chat_save_url+'api/user/chat_messages_save?from_user_id='+data.from_user_id
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