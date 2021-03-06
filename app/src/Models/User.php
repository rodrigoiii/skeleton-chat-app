<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\ContactRequest;
use App\Models\Message;
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

    public function from_messages()
    {
        return $this->hasMany(Message::class, "from_id");
    }

    public function to_messages()
    {
        return $this->hasMany(Message::class, "to_id");
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function contact_requests()
    {
        return $this->hasMany(ContactRequest::class, "from_id");
    }

    public function isFriend(User $user)
    {
        $friend = $this->contacts()->where("contact_id", $user->getId())->first();
        return !is_null($friend);
    }

    public function hasPendingRequestTo(User $user)
    {
        $request = $this->contact_requests()
                    ->where("to_id", $user->getId())
                    ->accepted(false)
                    ->first();

        return !is_null($request);
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
            ->where('to_id', $user->getId());
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

    public function unreadMessage(User $from)
    {
        return $this->to_messages()
                    ->isRead(false)
                    ->where("from_id", $from->getId());
    }

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    public static function findByLoginToken($login_token)
    {
        if (!is_null($login_token))
        {
            return static::where('login_token', $login_token)->first();
        }
    }
}
