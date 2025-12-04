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

class SensorServer implements MessageComponentInterface
{
    protected $clients;
    protected $sensorData;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->sensorData = [];
        echo "Servidor WebSocket iniciado\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
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

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Conexión cerrada: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = Factory::create();

// Configuración SSL - uso directo de las rutas de certificado/clave que proporcionaste
// Ajusta aquí si tus rutas cambian. Las rutas que indicaste fueron (considera solo nombres):
//  - _.castelancarpinteyro.com_private_key.key
//  - _.castelancarpinteyro.com_private_key.pfx
//  - _.castelancarpinteyro.com_ssl_certificate_INTERMEDIATE.zip
//  - 2025-2026_CC.COM.pem
//  - castelancarpinteyro.com_ssl_certificate.cer

// Rutas fijas (reemplaza si difieren ligeramente en tu sistema)
$foundKey = '../SSL 2025-2026 CASTELANCARPINTEYRO.COM/_.castelancarpinteyro.com_private_key.key';
$foundPfx = '../SSL 2025-2026 CASTELANCARPINTEYRO.COM/_.castelancarpinteyro.com_private_key.pfx';
$foundCert = '../SSL 2025-2026 CASTELANCARPINTEYRO.COM/castelancarpinteyro.com_ssl_certificate.cer';
$foundPem = '../SSL 2025-2026 CASTELANCARPINTEYRO.COM/2025-2026_CC.COM.pem';

// Si prefieres usar el .pem en lugar del .cer, descomenta la siguiente línea:
// $foundCert = $foundPem;

// Nota: si solo tienes .pfx, necesitas convertirlo a .pem/.key fuera de este script.

echo "Usando certificado: " . ($foundCert ?: 'none') . "\n";
echo "Usando clave: " . ($foundKey ?: ($foundPfx ?: 'none')) . "\n";

// Control de verificación de peer (puedes cambiarlo con variable de entorno WS_SSL_VERIFY=0)
$verifyPeer = getenv('WS_SSL_VERIFY');
$verifyPeer = ($verifyPeer === null) ? false : (bool)intval($verifyPeer);

$webSock = new Server('0.0.0.0:2005', $loop);
$sslOptions = [
    'local_cert' => $foundCert
];
if ($foundKey) {
    $sslOptions['local_pk'] = $foundKey;
} elseif ($foundPfx) {
    // Si se proporciona PFX pero no .key, indicarlo en logs (no lo desempaquetamos automáticamente)
    echo "PFX encontrado: use una copia .pem/.key si es necesario: $foundPfx\n";
}
$sslOptions['verify_peer'] = $verifyPeer;
$sslOptions['allow_self_signed'] = !$verifyPeer;

$webSock = new SecureServer($webSock, $loop, $sslOptions);

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
