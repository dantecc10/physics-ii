<?php
// server.php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

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
        
        // Procesar datos JSON del ESP32
        $data = json_decode($msg, true);
        
        if ($data !== null) {
            // Agregar timestamp si no viene incluido
            if (!isset($data['timestamp'])) {
                $data['timestamp'] = date('Y-m-d H:i:s');
            }
            
            // Guardar datos del sensor
            $this->sensorData[] = $data;
            
            // Guardar en archivo (opcional)
            file_put_contents(
                'sensor_data.json', 
                json_encode($this->sensorData, JSON_PRETTY_PRINT)
            );
            
            // Reenviar a todos los clientes conectados
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode([
                        'type' => 'sensor_update',
                        'data' => $data
                    ]));
                }
            }
            
            // Confirmar recepción al ESP32
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

// Crear servidor en puerto 9696
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SensorServer()
        )
    ),
    2005,
    '0.0.0.0'
);

echo "Servidor WebSocket ejecutándose en puerto 9696\n";
echo "Esperando conexiones...\n";

$server->run();
