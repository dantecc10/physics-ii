<?php
require_once __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "New message: $msg\n";
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = \React\EventLoop\Factory::create();
$webSock = new \React\Socket\Server('0.0.0.0:8080', $loop);

$webSock = new \React\Socket\SecureServer($webSock, $loop, [
    'local_cert' => __DIR__ . '/certificate.pem', // Ruta a tu archivo .pem
    'local_pk' => __DIR__ . '/certificate.pem',   // Ruta a tu archivo .pem (clave privada)
    'allow_self_signed' => true,
    'verify_peer' => false
]);

$webServer = new IoServer(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    $webSock
);

echo "Server running on wss://sgi.castelancarpinteyro.com:8080\n";

$loop->run();
