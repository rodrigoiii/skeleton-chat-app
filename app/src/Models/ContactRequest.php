<?php

namespace App\Models;

use Core\BaseModel;
use Core\Log;

class ContactRequest extends BaseModel
{
    protected $fillable = ["from_id", "to_id", "is_accepted"];

    public function scopeAccepted($query, $accepted=true)
    {
        return $query->where("is_accepted", $accepted);
    }

    public static function hasRequest(User $user, User $user2, $accepted=false)
    {
        return static::where(function($query) use($user, $user2) {
            return $query->where("from_id", $user->getId())
                        ->where("to_id", $user2->getId());
        })->orWhere(function($query) use($user, $user2) {
            return $query->where("from_id", $user2->getId())
                        ->where("to_id", $user->getId());
        })
        ->accepted($accepted)
        ->get()
        ->isNotEmpty();
    }

    public static function send($from, $to, $notif=true)
    {
        // send contact request
        $result = static::create([
            'from_id' => $from->getId(),
            'to_id' => $to->getId()
        ]);

        if ($result instanceof static)
        {
            if ($notif)
            {
                $notif = Notification::createSendRequest($from, $to);

                return $notif instanceof Notification;
            }

            return true;
        }

        Log::error("Contact Request Error: Creating contact request is not working!");
        return false;
    }

    public static function accept($from, $to, $notif=true)
    {
        $contactRequest = static::accepted(false)
                            ->where("from_id", $from->getId())
                            ->where("to_id", $to->getId())
                            ->first();

        if (!is_null($contactRequest))
        {
            $contactRequest->is_accepted = true;
            $is_accepted = $contactRequest->save();

            $contact = Contact::create([
                'contact_id' => $to->getId(),
                'user_id' => $from->getId()
            ]);
            $is_saved = $contact instanceof Contact;

            if ($is_accepted && $is_saved)
            {
                if ($notif)
                {
                    return Notification::changeToAcceptRequest($from, $to);
                }

                return true;
            }

            Log::error("Contact Request Error: Accepting request is not working!");
        }

        return false;
    }
}
