<?php

namespace App\Controllers;

use App\Models\ContactRequest;
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
        $id = 2; // assume auth id

        $authUser = User::find($id);
        $contacts = $authUser->contacts;
        $contactRequests = $authUser->contact_requests();
        $notifications = $authUser->notifications();

        return $this->view->render($response, "chat/chat.twig", compact("contacts", "contactRequests", "notifications"));
    }

    public function searchContacts(Request $request, Response $response)
    {
        // $login_token = $request->getParam('login_token');
        // $authUser = User::findByLoginToken($login_token);

        $id = 2; // assume auth id
        $authUser = User::find($id);

        $keyword = $request->getParam('keyword');

        // $contact_ids = $authUser->contacts()->pluck('user_id')->toArray();
        // $contact_requests_ids = $authUser->contact_requests()->pluck('to_id')->toArray();

        // unsearchable ids
        // $ignore_user_ids = array_flatten([$contact_ids, $contact_requests_ids, $authUser->id]);

        $result = User::search($keyword)
                    ->whereNotIn('id', [$id])
                    ->get();

        $users = transformer($result, new SearchContactResultTransformer($authUser))->toArray();

        return $response->withJson([
            'success' => true,
            'users' => $users['data']
        ]);
    }

    public function sendContactRequest(Request $request, Response $response)
    {
        $id = 2; // assume auth id

        $authUser = User::find($id);

        // $login_token = $request->getParam('login_token');
        // $authUser = User::findByLoginToken($login_token);
        $to_id = $request->getParam('to_id');

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
