<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use React\Socket\SecureServer;

require 'vendor/autoload.php';

class HumidityServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = LoopFactory::create();
$webSock = new Reactor('0.0.0.0:8080', $loop);

$context = [
    'local_cert' => __DIR__ . '/certificlate.pem', // Ruta a tu archivo .pem
    'local_pk' => __DIR__ . '/private_key.pem',   // Ruta a tu archivo de clave privada .pem
    'allow_self_signed' => true,
    'verify_peer' => false,
];

$secure_webSock = new SecureServer($webSock, $loop, $context);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new HumidityServer()
        )
    ),
    $secure_webSock,
    $loop
);

$server->run();
