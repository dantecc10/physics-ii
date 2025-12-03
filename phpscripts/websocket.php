<?php 
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyWebSocket implements MessagecomponentInterface {
    public $clients;
    private $connectedClients;

    public function __construct(){
        $this->clients = new \SplObjectStorage;
        $this->connectedClients = [];
            
    }

    public function onOpen (ConnectionInterface $conn){
        $this->clients->attach($conn);
        $this->connectedClients[$conn->resourceId] = $conn;
        echo "Nueva coneccion! ({$conn->resourceId})\n";
        $conn->send("Bienvenido a el servidor.");
            }
            public function onMessage(ConnectionInterface $from, $msg){
                echo $msg . "\n";
                foreach ($this->connectedClients as $client){
                    $client->send($msg);
                }
                            }
            public function onClose (ConnectionInterface $conn){
                echo "conexion cerrada! ({$conn->resourceId})\n";
                $conn->close();
            }
            public function onError(ConnectionInterface $conn, \Exception $e){
                echo "An error occurred:" . $e ->getMessage() . "\n";
                $conn->close();
            }
} 
$app = new Ratchet\App("192.168.0.9", 81,"0.0.0.0");
$app->route('/', new MyWebSocket, array('*'));

$app->run();
?>