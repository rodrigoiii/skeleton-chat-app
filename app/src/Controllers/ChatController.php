<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Models\Contact;
use App\Models\ContactRequest;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Transformer\SearchContactResultTransformer;
use Core\BaseController;
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
        $login_token = $request->getParam('login_token');
        $authUser = User::findByLoginToken($login_token);

        if (!is_null($authUser))
        {
            $keyword = $request->getParam('keyword');

            // $contact_ids = $authUser->contacts()->pluck('user_id')->toArray();
            // $contact_requests_ids = $authUser->contact_requests()->pluck('to_id')->toArray();

            // unsearchable ids
            // $ignore_user_ids = array_flatten([$contact_ids, $contact_requests_ids, $authUser->id]);

            $result = User::search($keyword)
                        ->whereNotIn('id', [$authUser->getId()])
                        ->get();

            $users = transformer($result, new SearchContactResultTransformer($authUser))->toArray();

            return $response->withJson([
                'success' => true,
                'users' => $users['data']
            ]);
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function sendContactRequest(Request $request, Response $response)
    {
        $login_token = $request->getParam('login_token');
        $authUser = User::findByLoginToken($login_token);

        if (!is_null($authUser))
        {
            $to_id = $request->getParam('to_id');

            if (!is_null($to_id))
            {
                $is_sent = ContactRequest::send($authUser->id, $to_id);

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
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function acceptRequest(Request $request, Response $response)
    {
        $login_token = $request->getParam('login_token');
        $authUser = User::findByLoginToken($login_token);

        if (!is_null($authUser))
        {
            $from_id = $request->getParam('from_id');

            if (!is_null($from_id))
            {
                $is_accepted = ContactRequest::accept($from_id, $authUser->id);
                $notif = Notification::acceptRequestType()
                            ->where("from_id", $from_id)
                            ->where("to_id", $authUser->id)
                            ->first();

                return $response->withJson($is_accepted ?
                    [
                        'success' => true,
                        'message' => "Successfully accept request.",
                        'notif_message' => $notif->getMessage($authUser)
                    ] :
                    [
                        'success' => false,
                        'message' => "Cannot accept request this time. Please try again later."
                    ]
                );
            }
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function readNotification(Request $request, Response $response)
    {
        $login_token = $request->getParam('login_token');
        $authUser = User::findByLoginToken($login_token);

        if (!is_null($authUser))
        {
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

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }

    public function sendMessage(Request $request, Response $response, $to_id)
    {
        $login_token = $request->getParam('login_token');
        $authUser = User::findByLoginToken($login_token);

        $message = $request->getParam('message');

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
            'success' => true,
            'message' => "Cannot send message this time. Please try again later."
        ]);
    }
}
