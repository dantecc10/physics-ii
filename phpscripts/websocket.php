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
$app = new Ratchet\App("192.168.0.9", 81, "0.0.0.0");
$app->route('/', new MyWebSocket(), array('*'));

$app->run();
