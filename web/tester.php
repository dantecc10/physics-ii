<!DOCTYPE html>
<html>
<body>
<h1>Test WebSocket</h1>
<div id="log"></div>
<script>
const log = document.getElementById('log');
log.innerHTML += 'Conectando...<br>';

const ws = new WebSocket('ws://realrecursantes.castelancarpinteyro.com:2005');

ws.onopen = () => {
    log.innerHTML += '✅ CONECTADO!<br>';
    ws.send(JSON.stringify({test: 'hello'}));
};

ws.onmessage = (e) => {
    log.innerHTML += '📨 Mensaje: ' + e.data + '<br>';
};

ws.onerror = (e) => {
    log.innerHTML += '❌ Error: ' + e.type + '<br>';
};

ws.onclose = (e) => {
    log.innerHTML += '🔌 Cerrado: ' + e.code + '<br>';
};
</script>
</body>
</html>