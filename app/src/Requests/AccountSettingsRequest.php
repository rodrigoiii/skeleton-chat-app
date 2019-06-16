<?php

namespace App\Requests;

use App\Auth\Auth;
use Core\BaseRequest;
use Respect\Validation\Validator as v;

/**
 * Requirements:
 * - UserMiddleware was used
 */
class AccountSettingsRequest extends BaseRequest
{
    /**
     * Change password rules
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();
        $inputs = $this->request->getParams();

        return [
            'picture' => v::optionalFile(v::uploaded()->file()->image()->size(null, "5mb")),
            'first_name' => v::notEmpty()->alpha(),
            'last_name' => v::notEmpty()->alpha(),
            'current_password' => $this->isPasswordModify() ? v::passwordVerify($user->password) : v::alwaysValid(),
            'new_password' => $this->isPasswordModify() ? v::passwordStrength() : v::alwaysValid(),
            'confirm_new_password' => $this->isPasswordModify() ? v::passwordMatch($inputs['new_password']) : v::alwaysValid(),
        ];
    }

    private function isPasswordModify()
    {
        $inputs = $this->request->getParams();

        return !empty($inputs['current_password']) ||
                !empty($inputs['new_password']) ||
                !empty($inputs['confirm_new_password']);
    }
}
