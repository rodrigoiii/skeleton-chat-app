<?php

namespace App\Chat;

class EventHandler
{
    protected $clients;

    public function __construct() {
        $this->clients = [];
    }

    public function onConnectionEstablish(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);

        $clients = $this->clients;

        foreach ($clients as $user_id => $client) {
            if ($client !== $from)
            {
                if (!is_null($authUser->chatStatus)) $authUser->chatStatus->setAsOnline();
                else ChatStatus::createOnlineUser($authUser);

                $return_data = [
                    'event' => __FUNCTION__,
                    'success' => !is_null($result),
                    'auth_user_id' => $authUser->getId(),
                    'token' => User::find($user_id)->login_token
                ];

                $client->send(json_encode($return_data));
            }
        }
    }
}
