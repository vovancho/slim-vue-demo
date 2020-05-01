const WebSocket = require('ws')

const fs = require('fs')
const jwt = require('jsonwebtoken')
const dotenv = require('dotenv')
const amqp = require('amqplib/callback_api')

dotenv.load()

const server = new WebSocket.Server({ port: 8000 })
const jwtKey = fs.readFileSync(process.env.WS_JWT_PUBLIC_KEY)

server.on('connection', function (ws, request) {
  console.log('connected:', request.connection.remoteAddress)

  ws.on('message', function (message) {
    const data = JSON.parse(message)
    if (data.type && data.type === 'auth') {
      try {
        const token = jwt.verify(data.token, jwtKey, { algorithms: ['RS256'] })
        ws.user_id = token.sub
      } catch (err) {
        console.log(err)
      }
    }
  })
})

amqp.connect(process.env.WS_AMQP_URI, function (err, conn) {
  if (err) {
    console.log(err)
    return
  }
  conn.createChannel(function (connErr, ch) {
    const queue = 'tasks.notifications.queue'
    ch.consume(
      queue,
      function (message) {
        const value = JSON.parse(message.content)

        server.clients.forEach((ws) => {
          if (ws.user_id) {
            switch (value.visibility) {
              case 'private':
                if (ws.user_id === value.author_id) {
                  ws.send(message.content.toString())
                }
                break
              case 'public':
                ws.send(message.content.toString())
                break
            }
          }
        })
      },
      { noAck: true }
    )
  })
})
