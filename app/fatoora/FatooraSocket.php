<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class FatooraWebSocket implements MessageComponentInterface {
    protected $clients;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage();
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        // This WebSocket server will only receive messages, not send them
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // Method to broadcast a message to all connected clients
    public function sendToAll($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}


