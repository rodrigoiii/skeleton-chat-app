<?php

namespace App\Models;

use App\Models\Contact;
use App\Traits\FullTextSearch;
use Core\BaseModel;

class User extends BaseModel
{
    use FullTextSearch;

    protected $fillable = ["picture", "first_name", "last_name", "email", "password", "login_token"];
    protected $searchable = ["first_name", "last_name"];
    protected $hidden = ["password"];

    const PLACEHOLDER_IMAGE = "/img/fa-image.png";

    public function contacts()
    {
        return $this->hasMany(Contact::class, "user_id");
    }

    public function setLoginToken($login_token)
    {
        $this->login_token = $login_token;
        return $this->save();
    }

    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getPicture($placeholder = false)
    {
        if (!is_null($this->picture))
        {
            return $this->picture;
        }

        return $placeholder ? static::PLACEHOLDER_IMAGE : null;
    }

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }
}
