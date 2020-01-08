let WebSocket = require('ws');

let fs = require('fs');
let jwt = require('jsonwebtoken');
let dotenv = require('dotenv');
let amqp = require('amqplib/callback_api');

dotenv.load();

let server = new WebSocket.Server({ port: 8000 });
let jwtKey = fs.readFileSync(process.env.WS_JWT_PUBLIC_KEY);

server.on('connection', function (ws, request) {
  console.log('connected: %s', request.connection.remoteAddress);

});

amqp.connect(process.env.WS_AMQP_URI, function(err, conn) {
  if (err) {
    console.log(err);
    return;
  }

});