<?php

namespace App\Models;

use App\Models\User;
use Core\BaseModel;

class Contact extends BaseModel
{
    protected $fillable = ["contact_id", "user_id"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function contacts(User $user)
    {
        return static::where('contact_id', $user->getId())
                    ->orWhere('user_id', $user->getId());
    }
}
