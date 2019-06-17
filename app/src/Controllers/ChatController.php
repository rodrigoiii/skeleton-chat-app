<?php

namespace App\Controllers;

use App\Models\User;
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
}
