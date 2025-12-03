<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Chat</title>
</head>

<body>
    <h1>WebSocket Chat</h1>
    <input type="text" id="messageInput" placeholder="Type a message">
    <button id="sendButton">Send</button>
    <ul id="messages"></ul>

    <script>
        const ws = new WebSocket('wss://sgi.castelancarpinteyro.com:8080');

        ws.onopen = () => {
            console.log('Connected to the WebSocket server');
        };

        ws.onmessage = (event) => {
            const messages = document.getElementById('messages');
            const messageItem = document.createElement('li');
            messageItem.textContent = event.data;
            messages.appendChild(messageItem);
        };

        ws.onclose = () => {
            console.log('Disconnected from the WebSocket server');
        };

        document.getElementById('sendButton').onclick = () => {
            const input = document.getElementById('messageInput');
            const message = input.value;
            ws.send(message);
            input.value = '';
        };
    </script>
</body>

</html>