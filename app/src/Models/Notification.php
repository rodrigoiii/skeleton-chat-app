<?php

namespace App\Models;

use App\Models\User;
use Core\BaseModel;

class Notification extends BaseModel
{
    const SEND_REQUEST_MESSAGE = "send request to you.";

    protected $fillable = ["by_id", "to_id", "is_read", "message"];

    public function scopeRead($query, $is_read=true)
    {
        return $query->where("is_read", $is_read);
    }

    public static function createSendRequest($by_id, $to_id)
    {
        $to = User::find($to_id);

        return static::create([
            'by_id' => $by_id,
            'to_id' => $to_id,
            'message' => $to->getFullName() . " " . static::SEND_REQUEST_MESSAGE
        ]);
    }
}
