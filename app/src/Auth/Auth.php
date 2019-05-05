<?php

namespace App\Auth;

use App\Models\User;
use Core\Log;
use Core\Utilities\Session;
use Exception;

class Auth
{
    /**
     * Check user credential
     *
     * @param  string $email
     * @param  string $password
     * @return User|false
     */
    public static function validateCredential($email, $password)
    {
        $user = User::findByEmail($email);

        try {
            if (is_null($user)) throw new Exception("{$email} is not exist.", 1);
            if (!password_verify($password, $user->password)) throw new Exception("{$email} is valid but password is incorrect.", 1);

            return $user;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve logged in user
     *
     * @return User|null
     */
    public static function user()
    {
        if (!is_null(Session::get('user_auth_id')))
        {
            return User::find(Session::get('user_auth_id'));
        }

        return null;
    }

    /**
     * Log in the user
     *
     * @param  id $user_id
     * @return void
     */
    public static function logInByUserId($user_id)
    {
        $login_token = uniqid();

        $user = User::find($user_id);

        if (!is_null($user))
        {
            Log::info("Login: ". $user->getFullName());

            Session::set('user_auth_id', $user_id);
            Session::set('user_login_token', $login_token);

            $user->setLoginToken($login_token);
        }
        else
        {
            Log::error("User id {$user_id} is not exist.");
        }
    }

    /**
     * Log out the user
     *
     * @return void
     */
    public static function logOut()
    {
        $user_id = Session::get('user_auth_id');
        $user = User::find($user_id);

        if (!is_null($user))
        {
            Log::info("Logout: ". $user->getFullName());
            Session::destroy(['user_auth_id', 'user_login_token']);
        }
        else
        {
            Log::error("User id {$user_id} is not exist.");
        }
    }

    /**
     * Check if user still log in
     *
     * @return boolean
     */
    public static function check()
    {
        $user_id = Session::get('user_auth_id');
        $user = User::find($user_id);

        if (!is_null($user))
        {
            $is_token_valid = $user->login_token === Session::get('user_login_token');

            if ($is_token_valid)
            {
                return true;
            }

            static::logout();
        }

        return false;
    }
}
