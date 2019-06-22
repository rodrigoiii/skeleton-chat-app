<?php

namespace App\Chat;

use App\Models\ChatStatus;
use App\Models\User;
use Ratchet\ConnectionInterface;

class EventHandler
{
    protected $clients;

    public function __construct() {
        $this->clients = [];
    }

    public function onConnected(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);

        $clients = $this->clients;

        foreach ($clients as $user_id => $client) {
            if ($client !== $from)
            {
                if (!is_null($authUser->chatStatus)) $authUser->chatStatus->setAsOnline();
                else ChatStatus::createOnlineUser($authUser);

                $receiver = User::find($user_id);

                if (!is_null($receiver))
                {
                    $return_data = [
                        'event' => __FUNCTION__,
                        'emitter_id' => $authUser->getId(),
                        'receiver_token' => $receiver->login_token
                    ];

                    $client->send(json_encode($return_data));
                }
            }
        }
    }

    public function onDisconnected(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);

        $clients = $this->clients;

        foreach ($clients as $user_id => $client) {
            if ($client !== $from)
            {
                if (!is_null($authUser->chatStatus)) $authUser->chatStatus->setAsOnline();
                else ChatStatus::createOnlineUser($authUser);

                $receiver = User::find($user_id);

                if (!is_null($receiver))
                {
                    $return_data = [
                        'event' => __FUNCTION__,
                        'emitter_id' => $authUser->getId(),
                        'receiver_token' => $receiver->login_token
                    ];

                    $client->send(json_encode($return_data));
                }
            }
        }
    }

    public function onTyping(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);
        $chattingTo = User::find($msg->chatting_to_id);

        // if chatting to is online
        if (isset($this->clients[$msg->chatting_to_id]))
        {
            $return_data = [
                'event' => __FUNCTION__,
                // 'chatting_from' => [
                //     'id' => $authUser->id,
                //     'picture' => $authUser->picture
                // ],
                'receiver_token' => $receiver->login_token
            ];

            $chattingToSocket = $this->clients[$msg->chatting_to_id];
            $chattingToSocket->send(json_encode($return_data));
        }
    }
}
