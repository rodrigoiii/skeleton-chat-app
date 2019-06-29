<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Models\Contact;
use App\Models\ContactRequest;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Transformers\ConversationTransformer;
use App\Transformers\SearchContactResultTransformer;
use Core\BaseController;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ChatController extends BaseController
{
    /**
     * [index description]
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function index(Response $response)
    {
        $authUser = Auth::user();
        $contacts = Contact::contacts($authUser)->get();

        $notifications = Notification::getAll($authUser)->latest();
        $notif_num = Notification::numOfUnread($authUser);

        return $this->view->render($response, "chat/chat.twig", compact("contacts", "notifications", "notif_num"));
    }

    public function searchContacts(Request $request, Response $response)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        $keyword = $request->getParam("keyword");

        $result = User::search($keyword)
                    ->where("id", "<>", $authUser->getId()) // exclude self in result
                    ->get();

        $users = transformer($result, new SearchContactResultTransformer($authUser))->toArray();

        return $response->withJson([
            'success' => true,
            'users' => $users['data']
        ]);
    }

    public function sendContactRequest(Request $request, Response $response)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        $to_id = $request->getParam("to_id");
        $to = User::find($to_id);

        if (!is_null($to))
        {
            if (!ContactRequest::hasRequest($authUser, $to))
            {
                if (!ContactRequest::areFriends($authUser, $to))
                {
                    $is_sent = ContactRequest::send($authUser, $to);

                    return $response->withJson($is_sent ?
                        [
                            'success' => true,
                            'message' => "Successfully send request."
                        ] :
                        [
                            'success' => false,
                            'message' => "Cannot send request this time. Please try again later."
                        ]
                    );
                }
                else
                {
                    Log::warning("Warning: " . $to->getFullName() . " is already contact of " . $authUser->getFullName());
                }
            }
            else
            {
                Log::warning("Warning: " . $authUser->getFullName() . " has already request to " . $to->getFullName());
            }
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function acceptRequest(Request $request, Response $response)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        $from_id = $request->getParam("from_id");
        $from = User::find($from_id);

        if (!is_null($from))
        {
            if (ContactRequest::hasRequest($from, $authUser))
            {
                if (!ContactRequest::areFriends($from, $authUser))
                {
                    $is_accepted = ContactRequest::accept($from, $authUser);
                    $notif = Notification::acceptRequestType()
                                ->where("from_id", $from_id)
                                ->where("to_id", $authUser->id)
                                ->first();

                    return $response->withJson($is_accepted ?
                        [
                            'success' => true,
                            'message' => "Successfully accept request.",
                            'notif_message' => $notif->getMessage($authUser),
                            'requester' => [
                                'id' => $from->getId(),
                                'online' => $from->chatStatus->isOnline(),
                                'picture' => $from->getPicture(true),
                                'full_name' => $from->getFullName()
                            ]
                        ] :
                        [
                            'success' => false,
                            'message' => "Cannot accept request this time. Please try again later."
                        ]
                    );
                }
            }
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function readNotification(Request $request, Response $response)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        $changed = Notification::markAsRead($authUser);

        return $response->withJson($changed ?
            [
                'success' => true,
                'message' => "Successfully mark notification as read."
            ] :
            [
                'success' => false,
                'message' => "Cannot mark notification as read this time. Please try again later."
            ]
        );
    }

    public function sendMessage(Request $request, Response $response, $to_id)
    {
        $message = $request->getParam("message");

        if (strlen($message) > 0 && strlen($message) <= 1000)
        {
            $login_token = $request->getParam("login_token");
            $authUser = User::findByLoginToken($login_token);

            $sentMessage = $authUser->sendMessage(new Message(compact("message", "to_id")));

            if ($sentMessage instanceof Message)
            {
                return $response->withJson([
                    'success' => true,
                    'message' => "Successfully send message.",
                    'sent_message' => [
                        'id' => $sentMessage->id,
                        'message' => $sentMessage->message
                    ]
                ]);
            }

            return $response->withJson([
                'success' => false,
                'message' => "Cannot send message this time. Please try again later."
            ]);
        }

        return $response->withJson([
            'success' => false,
            'message' => "You cannot send message more than 1000 characters."
        ]);
    }

    public function getConversation(Request $request, Response $response, $to_id)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);
        $user2 = User::find($to_id);

        $conversation = Message::conversation($authUser, $user2)
                            ->select(["id", "message", "from_id", "to_id", "created_at"])
                            ->lastNumMessages();

        $conversation = transformer($conversation, new ConversationTransformer)->toArray();

        return $response->withJson([
            'success' => true,
            'message' => "Successfully fetch message.",
            'conversation' => $conversation['data']
        ]);
    }

    public function getMessagesByBatch(Request $request, Response $response, $to_id)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);
        $user2 = User::find($to_id);

        $batch = $request->getParam("batch");

        $default_convo_length = config('chat.default_conversation_length');

        $conversation = Message::conversation($authUser, $user2)
                            ->select(["id", "message", "from_id", "to_id", "created_at"])
                            ->offset($default_convo_length * $batch)
                            ->lastNumMessages();

        $conversation = transformer($conversation, new ConversationTransformer)->toArray();

        return $response->withJson([
            'success' => true,
            'message' => "Successfully fetch message.",
            'conversation' => $conversation['data']
        ]);
    }

    public function readMessage(Request $request, Response $response, $to_id)
    {
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        $user2 = User::find($to_id);

        $markAsRead = $authUser->unreadMessage($user2)->update(['is_read' => Message::IS_READ]);

        if ($markAsRead)
        {
            return $response->withJson([
                'success' => true,
                'message' => "Successfully mark message as read.",
            ]);
        }

        return $response->withJson([
            'success' => false,
            'message' => "Cannot mark message as read this time. Please try again later."
        ]);
    }
}
