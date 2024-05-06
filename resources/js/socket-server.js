const io = require('socket.io')(3000);

io.on('connection', (socket) => {
    console.log('A client connected');

    // Example: Send message to client
    socket.emit('message', 'Hello from server!');

    socket.on('disconnect', () => {
        console.log('A client disconnected');
    });
});
