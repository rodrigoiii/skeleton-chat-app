<?php

namespace App\Models;

use App\Models\Notification;
use Core\BaseModel;
use Core\Log;

class ContactRequest extends BaseModel
{
    protected $fillable = ["by_id", "to_id", "is_read", "is_accepted"];

    public function scopeRead($query, $is_read=true)
    {
        return $query->where("is_read", $is_read);
    }

    public static function send($by_id, $to_id, $notif=true)
    {
        // send contact request
        $result = static::create([
            'by_id' => $by_id,
            'to_id' => $to_id
        ]);

        if ($result instanceof static)
        {
            $notifResult = Notification::createSendRequest($by_id, $to_id);

            return $notifResult instanceof Notification;
        }

        Log::error("Contact Request Error: Creating contact request is not working!");
        return false;
    }
}
