<?php

namespace App\Models;

use App\Models\User;
use Core\BaseModel;
use Core\Log;

class Notification extends BaseModel
{
    const SEND_REQUEST = "send-request";
    const ACCEPT_REQUEST = "accept-request";
    const CUSTOM = "custom";
    const IS_UNREAD = 0;
    const IS_READ = 1;

    protected $fillable = ["from_id", "to_id", "type", "message", "is_read"];

    public function scopeSendRequestType($query)
    {
        return $query->where("type", static::SEND_REQUEST);
    }

    public function scopeAcceptRequestType($query)
    {
        return $query->where("type", static::ACCEPT_REQUEST);
    }

    public function scopeRead($query, $is_read=true, $userReceiver=null)
    {
        $query = $query->where("is_read", $is_read);

        if ($userReceiver instanceof User)
        {
            $query = $query->where("to_id", $userReceiver->getId());
        }

        return $query;
    }

    public function isSendRequest($userReceiver=null)
    {
        $result = $this->type === static::SEND_REQUEST;

        if ($userReceiver instanceof User)
        {
            $result = $result && $userReceiver->getId() === $this->to_id;
        }

        return $result;
    }

    public function getUser()
    {
        switch ($this->type)
        {
            case static::SEND_REQUEST:
                return User::find($this->from_id);

            case static::ACCEPT_REQUEST:
                return User::find($this->to_id);

            default:
                Log::error("Error getting user: Notification type " . $this->type . " is invalid.");
        }

        return null;
    }

    public function getMessage(User $owner)
    {
        $from = User::find($this->from_id);
        $to = User::find($this->to_id);

        if (is_null($from) || is_null($to))
        {
            Log::error("Error on getting message: Neither From ID " . $this->from_id . " nor To ID " . $this->to_id . " is invalid.");
            return null;
        }

        switch ($this->type)
        {
            case static::SEND_REQUEST:
                if ($from->getId() === $owner->getId())
                {
                    return "You send request to " . $to->getFullName();
                }

                if ($to->getId() === $owner->getId())
                {
                    return $from->getFullName() . " send request to you.";
                }

            case static::ACCEPT_REQUEST:
                if ($from->getId() === $owner->getId())
                {
                    return "You and " . $to->getFullName() . " can now chat each other.";
                }

                if ($to->getId() === $owner->getId())
                {
                    return "You and " . $from->getFullName() . " can now chat each other.";
                }

            case static::CUSTOM:
                return $this->message;

            default:
                Log::error("Error getting notification message: Notification type " . $this->type . " is invalid.");
        }

        return null;
    }

    public static function getAll(User $user)
    {
        return static::where("from_id", $user->getId())
                    ->orWhere("to_id", $user->getId());
    }

    public static function createSendRequest($from_id, $to_id)
    {
        return static::create([
            'from_id' => $from_id,
            'to_id' => $to_id,
            'type' => static::SEND_REQUEST,
        ]);
    }

    public static function changeToAcceptRequest($from_id, $to_id, $markAsUnread=true)
    {
        $notif = static::sendRequestType()
                    ->where("from_id", $from_id)
                    ->where("to_id", $to_id)
                    ->first();

        if (!is_null($notif))
        {
            if ($markAsUnread) {
                $notif->is_read = static::IS_UNREAD;
            }

            $notif->type = static::ACCEPT_REQUEST;
            return $notif->save();
        }

        return false;
    }

    public static function markAsRead($from_id=null, $to_id=null)
    {
        $notifs = static::where("from_id", $from_id)
                    ->orWhere("to_id", $to_id);

        if ($notifs->get()->isNotEmpty())
        {
            $is_updated = $notifs->update(['is_read' => static::IS_READ]);
            return $is_updated;
        }

        return false;
    }
}
