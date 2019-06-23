<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\ContactRequest;
use App\Models\Notification;
use App\Traits\FullTextSearch;
use Core\BaseModel;

class User extends BaseModel
{
    use FullTextSearch;

    protected $fillable = ["picture", "first_name", "last_name", "email", "password", "login_token"];
    protected $searchable = ["first_name", "last_name"];
    protected $hidden = ["password"];

    const PLACEHOLDER_IMAGE = "/img/fa-image.png";

    public function chatStatus()
    {
        return $this->hasOne(ChatStatus::class);
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

    public function requestTo(User $user)
    {
        return ContactRequest::where('from_id', $this->id)
            ->where('to_id', $user->getId())
            ->first();
    }

    public function sendMessage(Message $message)
    {
        $message->from_id = $this->id;
        $is_sent = $message->save();

        return $is_sent ? $message : false;
    }

    public function eraseLoginToken()
    {
        $this->login_token = null;
        return $this->save();
    }

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    public static function findByLoginToken($login_token)
    {
        return static::where('login_token', $login_token)->first();
    }
}
