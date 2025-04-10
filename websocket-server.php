<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

class RealtimeDisplay implements MessageComponentInterface {
    protected $clients;
    protected $clientDevices; // Map to track client subscriptions by device

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->clientDevices = [];
        echo "[READY] WebSocket server started.\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->clientDevices[$conn->resourceId] = null; // Default to no device subscription
        echo "[CONNECT] New client ({$conn->resourceId}) connected.\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "[RECEIVE] $msg\n";
        $data = json_decode($msg, true);

        if (isset($data['device'])) {
            // Update the client's subscribed device
            $this->clientDevices[$from->resourceId] = $data['device'];
            echo "[SUBSCRIBE] Client {$from->resourceId} subscribed to device: {$data['device']}\n";
        } else {
            // Broadcast message to clients subscribed to the relevant device
            foreach ($this->clients as $client) {
                $subscribedDevice = $this->clientDevices[$client->resourceId];
                if ($subscribedDevice === null || $subscribedDevice === $data['device']) {
                    $client->send($msg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->clientDevices[$conn->resourceId]);
        echo "[DISCONNECT] Client {$conn->resourceId} disconnected.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[ERROR] {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(new WsServer(new RealtimeDisplay())),
    8080
);
$server->run();
