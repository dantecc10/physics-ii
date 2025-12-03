<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyWebSocket implements MessageComponentInterface
{
    public $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Nueva conexión! ({$conn->resourceId})\n";
        $conn->send("Bienvenido al servidor.");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo $msg . "\n";
        foreach ($this->clients as $client) {
            // enviar a todos (incluye al remitente). Si prefieres excluir al remitente,
            // añade: if ($from === $client) continue;
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // limpiar referencia al cliente
        $this->clients->detach($conn);
        echo "Conexión cerrada! ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

// Configurable via environment variables or CLI argument.
// Usage examples:
//  - env: WS_HOST, WS_PORT, WS_BIND
//    WS_HOST=realrecursantes.castelancarpinteyro.com WS_PORT=9000 php websocket.php
//  - CLI arg: php websocket.php 9000
$defaultDisplayHost = 'realrecursantes.castelancarpinteyro.com';
$displayHost = getenv('WS_HOST') ?: $defaultDisplayHost;
$port = getenv('WS_PORT') ?: (isset($argv[1]) ? intval($argv[1]) : 8080);
$bind = getenv('WS_BIND') ?: '0.0.0.0';

echo "Iniciando WebSocket en {$displayHost}:{$port} (bind {$bind})\n";
$app = new Ratchet\App($displayHost, (int)$port, $bind);
$app->route('/', new MyWebSocket(), array('*'));

$app->run();
