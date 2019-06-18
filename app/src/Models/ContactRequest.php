<?php

namespace App\Models;

use App\Models\Notification;
use Core\BaseModel;
use Core\Log;

class ContactRequest extends BaseModel
{
    protected $fillable = ["from_id", "to_id", "is_accepted"];

    public static function send($from_id, $to_id, $notif=true)
    {
        // send contact request
        $result = static::create([
            'from_id' => $from_id,
            'to_id' => $to_id
        ]);

        if ($result instanceof static)
        {
            $notifResult = Notification::createSendRequest($from_id, $to_id);

            return $notifResult instanceof Notification;
        }

        Log::error("Contact Request Error: Creating contact request is not working!");
        return false;
    }
}
