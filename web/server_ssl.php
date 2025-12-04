<?php
// server_ssl.php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\Socket\SecureServer;

class SensorServer implements MessageComponentInterface {
    protected $clients;
    protected $sensorData;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->sensorData = [];
        echo "Servidor WebSocket iniciado\n";
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido de ({$from->resourceId}): {$msg}\n";
        
        $data = json_decode($msg, true);
        
        if ($data !== null) {
            if (!isset($data['timestamp'])) {
                $data['timestamp'] = date('Y-m-d H:i:s');
            }
            
            $this->sensorData[] = $data;
            
            file_put_contents(
                'sensor_data.json', 
                json_encode($this->sensorData, JSON_PRETTY_PRINT)
            );
            
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode([
                        'type' => 'sensor_update',
                        'data' => $data
                    ]));
                }
            }
            
            $from->send(json_encode([
                'status' => 'success',
                'message' => 'Datos recibidos correctamente'
            ]));
        } else {
            echo "Error: Datos no válidos\n";
            $from->send(json_encode([
                'status' => 'error',
                'message' => 'Formato JSON inválido'
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Conexión cerrada: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = Factory::create();

// Configuración SSL - AJUSTA ESTAS RUTAS A TUS CERTIFICADOS
$webSock = new Server('0.0.0.0:2005', $loop);
$webSock = new SecureServer($webSock, $loop, [
    'local_cert'  => '/etc/letsencrypt/live/realrecursantes.castelancarpinteyro.com/fullchain.pem',
    'local_pk'    => '/etc/letsencrypt/live/realrecursantes.castelancarpinteyro.com/privkey.pem',
    'verify_peer' => false,
    'allow_self_signed' => true
]);

$webServer = new IoServer(
    new HttpServer(
        new WsServer(
            new SensorServer()
        )
    ),
    $webSock,
    $loop
);

echo "Servidor WebSocket SSL ejecutándose en puerto 2005\n";
echo "Esperando conexiones en wss://...\n";

$loop->run();