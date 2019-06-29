<?php

namespace App\Chat;

use App\Models\ChatStatus;
use App\Models\ContactRequest;
use App\Models\Notification;
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
                    if (ContactRequest::areFriends($authUser, $receiver))
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
    }

    public function onDisconnected(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);

        $clients = $this->clients;

        foreach ($clients as $user_id => $client) {
            if ($client !== $from)
            {
                if (!is_null($authUser->chatStatus)) $authUser->chatStatus->setAsOffline();
                else ChatStatus::createOfflineUser($authUser);

                $receiver = User::find($user_id);

                if (!is_null($receiver))
                {
                    if (ContactRequest::areFriends($authUser, $receiver))
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
    }

    public function onSendRequest(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);
        $to = User::find($msg->to_id);

        // if client is online
        if (isset($this->clients[$msg->to_id]))
        {
            if (ContactRequest::hasRequest($authUser, $to))
            {
                if (!ContactRequest::areFriends($authUser, $to))
                {
                    $notif = Notification::sendRequestType()
                                ->from($authUser)
                                ->to($to)
                                ->first();

                    $notif_placeholder = $authUser->getFullName() . " has send request to you.";

                    $return_data = [
                        'event' => __FUNCTION__,
                        'from' => [
                            'id' => $authUser->id,
                            'picture' => $authUser->picture,
                        ],
                        'notif_message' => !is_null($notif) ? $notif->getMessage($to) : $notif_placeholder,
                        'receiver_token' => $to->login_token
                    ];

                    $toSocket = $this->clients[$msg->to_id];
                    $toSocket->send(json_encode($return_data));
                }
                else
                {
                    echo "Warning: " . $to->getFullName() . " is already friend of " . $authUser->getFullName();
                }
            }
            else
            {
                echo "Warning: " . $authUser->getFullName() . " has no request to " . $to->getFullName() . " to emit it.";
            }
        }
    }

    public function onAcceptRequest(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);
        $requestFrom = User::find($msg->from_id);

        // if client is online
        if (isset($this->clients[$msg->from_id]))
        {
            if (ContactRequest::areFriends($requestFrom, $authUser))
            {
                $notif = Notification::sendRequestType()
                            ->from($requestFrom)
                            ->to($authUser)
                            ->first();

                $notif_placeholder = "You and " . $authUser->getFullName() . " can now chat each other.";

                $return_data = [
                    'event' => __FUNCTION__,
                    'accepter' => [
                        'id' => $authUser->id,
                        'picture' => $authUser->picture,
                        'full_name' => $authUser->getFullName(),
                    ],
                    'notif_message' => !is_null($notif) ? $notif->getMessage($authUser) : $notif_placeholder,
                    'receiver_token' => $requestFrom->login_token
                ];

                $toSocket = $this->clients[$msg->from_id];
                $toSocket->send(json_encode($return_data));
            }
            else
            {
                echo "Warning: " . $requestFrom->getFullName() . " is not friend of " . $authUser->getFullName() . " to emit accept request.";
            }
        }
    }

    public function onTyping(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);
        $to = User::find($msg->to_id);

        // if client is online
        if (isset($this->clients[$msg->to_id]))
        {
            $return_data = [
                'event' => __FUNCTION__,
                'from' => [
                    'id' => $authUser->id,
                    'picture' => $authUser->picture
                ],
                'receiver_token' => $to->login_token
            ];

            $toSocket = $this->clients[$msg->to_id];
            $toSocket->send(json_encode($return_data));
        }
    }

    public function onStopTyping(ConnectionInterface $from, $msg)
    {
        parse_str($from->httpRequest->getUri()->getQuery(), $params);

        $authUser = User::findByLoginToken($params['login_token']);
        $to = User::find($msg->to_id);

        // if client is online
        if (isset($this->clients[$msg->to_id]))
        {
            $return_data = [
                'event' => __FUNCTION__,
                'from' => [
                    'id' => $authUser->id,
                    'picture' => $authUser->picture
                ],
                'receiver_token' => $to->login_token
            ];

            $toSocket = $this->clients[$msg->to_id];
            $toSocket->send(json_encode($return_data));
        }
    }

    public function onSendMessage(ConnectionInterface $from, $msg)
    {
        $message = $msg->message;

        if (strlen($message) > 0 && strlen($message) <= 1000)
        {
            parse_str($from->httpRequest->getUri()->getQuery(), $params);

            $authUser = User::findByLoginToken($params['login_token']);
            $to = User::find($msg->to_id);

            // if client is online
            if (isset($this->clients[$msg->to_id]))
            {
                $return_data = [
                    'event' => __FUNCTION__,
                    'from' => [
                        'id' => $authUser->id,
                        'picture' => $authUser->picture,
                        'message' => $message,
                        'unread_message_number' => $to->unreadMessage($authUser)->get()->count()
                    ],
                    'receiver_token' => $to->login_token
                ];

                $toSocket = $this->clients[$msg->to_id];
                $toSocket->send(json_encode($return_data));
            }
        }
        else
        {
            echo "Warning: Cannot send message more than 1000 characters.";
        }
    }
}
