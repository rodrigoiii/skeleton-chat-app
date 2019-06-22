<?php

namespace App\Chat;

use App\Models\User;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat extends EventHandler implements MessageComponentInterface {

    public function __construct() {
        parent::__construct();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        parse_str($conn->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);

        if (!is_null($authUser))
        {
            $this->clients[$authUser->getId()] = $conn;
            echo "New connection! (".$conn->resourceId.")\n";
        }
        else
        {
            echo "Seems you are not authenticated!";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $msg = json_decode($msg);
        $event = $msg->event;
        unset($msg->event);

        $this->{$event}($from, $msg);
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $key = array_search($conn, $this->clients);
        unset($this->clients[$key]);

        $this->onDisconnected($conn, "");

        echo "Clients number: " . count($this->clients) . PHP_EOL;
        echo "Connection (".$conn->resourceId.") has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
