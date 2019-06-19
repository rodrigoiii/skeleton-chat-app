<?php

namespace App\Models;

use App\Models\Notification;
use Core\BaseModel;
use Core\Log;

class ContactRequest extends BaseModel
{
    protected $fillable = ["from_id", "to_id", "is_accepted"];

    public function scopeAccepted($query, $accepted=true)
    {
        return $query->where("is_accepted", $accepted);
    }

    public static function send($from_id, $to_id, $notif=true)
    {
        // send contact request
        $result = static::create([
            'from_id' => $from_id,
            'to_id' => $to_id
        ]);

        if ($result instanceof static)
        {
            if ($notif)
            {
                $notif = Notification::createSendRequest($from_id, $to_id);

                return $notif instanceof Notification;
            }

            return true;
        }

        Log::error("Contact Request Error: Creating contact request is not working!");
        return false;
    }

    public static function accept($from_id, $to_id, $notif=true)
    {
        $contactRequest = static::accepted(false)
                            ->where("from_id", $from_id)
                            ->where("to_id", $to_id)
                            ->first();

        if (!is_null($contactRequest))
        {
            $contactRequest->is_accepted = true;
            $is_saved = $contactRequest->save();

            if ($is_saved)
            {
                if ($notif)
                {
                    return Notification::changeToAcceptRequest($from_id, $to_id);
                }

                return true;
            }

            Log::error("Contact Request Error: Accepting request is not working!");
        }

        return false;
    }
}
