<?php

namespace App\Controllers;

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
        $id = 1; // assume auth id

        $authUser = User::find($id);
        $contacts = $authUser->contacts;

        return $this->view->render($response, "chat/chat.twig", compact("contacts"));
    }

    public function searchContacts(Request $request, Response $response)
    {
        // $login_token = $request->getParam('login_token');
        // $authUser = User::findByLoginToken($login_token);

        $id = 1; // assume auth id
        $authUser = User::find($id);

        $keyword = $request->getParam('keyword');

        // $contact_ids = $authUser->contacts()->pluck('user_id')->toArray();
        // $contact_requests_ids = $authUser->contact_requests()->pluck('to_id')->toArray();

        // unsearchable ids
        // $ignore_user_ids = array_flatten([$contact_ids, $contact_requests_ids, $authUser->id]);

        $result = User::search($keyword)
                    // ->whereNotIn('id', $ignore_user_ids)
                    ->get();

        $users = transformer($result, new SearchContactResultTransformer)->toArray();

        return $response->withJson([
            'success' => true,
            'users' => $users['data']
        ]);
    }
}
