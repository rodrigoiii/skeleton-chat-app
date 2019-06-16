<?php

namespace App\Controllers\Auth;

use App\Auth\Auth;
use App\Requests\AccountSettingsRequest;
use Core\BaseController;
use Psr\Http\Message\ResponseInterface as Response;

class AccountSettingsController extends BaseController
{
    /**
     * Display account settings page
     *
     * @param  Response $response
     * @return Response
     */
    public function getAccountSettings(Response $response)
    {
        return $this->view->render($response, "auth/account-settings.twig");
    }

    /**
     * Save the changes
     *
     * @param  AccountSettingsRequest $_request
     * @param  Response $response
     * @return Response
     */
    public function postAccountSettings(AccountSettingsRequest $_request, Response $response)
    {
        $inputs = $_request->getParams();
        $files = $_request->getUploadedFiles();

        $user = Auth::user();
        if ($files['picture']->getSize() > 0)
        {
            // delete old picture
            if (!is_null($user->picture))
            {
                if (file_exists($picture_path = public_path(trim($user->picture, "/"))))
                {
                    unlink($picture_path);
                }
            }

            $user->picture = upload($files['picture'], config('auth.account_settings.upload_path'));
        }
        $user->first_name = $inputs['first_name'];
        $user->last_name = $inputs['last_name'];
        if (!empty($inputs['new_password']))
        {
            $user->password = password_hash($inputs['new_password'], PASSWORD_DEFAULT);
        }

        if ($user->isDirty())
        {
            if ($user->save())
            {
                $this->flash->addMessage('success', "Your account was successfully updated!");
            }
            else
            {
                $this->flash->addMessage('error', "Updating account not working properly. Please try again later!");
            }
        }

        return $response->withRedirect($this->router->pathFor('auth.home'));
    }
}
