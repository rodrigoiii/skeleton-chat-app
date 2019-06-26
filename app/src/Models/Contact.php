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

    public static function contacts(User $user)
    {
        return static::where('contact_id', $user->getId())
                    ->orWhere('user_id', $user->getId());
    }
}
