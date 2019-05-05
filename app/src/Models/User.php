<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $fillable = ["first_name", "last_name", "email", "password", "login_token"];

    public function setLoginToken($login_token)
    {
        $this->login_token = $login_token;
        return $this->save();
    }

    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }
}
