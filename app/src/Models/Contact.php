<?php

namespace App\Models;

use Core\BaseModel;

class Contact extends BaseModel
{
    protected $fillable = ["contact_id", "user_id"];

    public function contact(User $owner)
    {
        switch ($owner->getId()) {
            case $this->contact_id:
                return $this->belongsTo(User::class, "user_id");

            case $this->user_id:
                return $this->belongsTo(User::class, "contact_id");

            default:
                return null;
        }
    }

    public static function isContact(User $user1, User $user2)
    {
        $result = static::where(function($query) use($user1, $user2) {
                    return $query->where('contact_id', $user1->getId())
                                ->where('user_id', $user2->getId());
                    })->orWhere(function($query) use($user1, $user2) {
                        return $query->where('contact_id', $user2->getId())
                                    ->where('user_id', $user1->getId());
                    })->first();

        return !is_null($result);
    }

    public static function contacts(User $user)
    {
        return static::where('contact_id', $user->getId())
                    ->orWhere('user_id', $user->getId());
    }
}
