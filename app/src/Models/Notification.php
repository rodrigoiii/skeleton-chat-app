<?php

namespace App\Models;

use App\Models\User;
use Core\BaseModel;
use Core\Log;

class Notification extends BaseModel
{
    const SEND_REQUEST_MESSAGE = "send request to you.";
    const SEND_REQUEST = "send-request";
    const ACCEPT_REQUEST = "accept-request";
    const CUSTOM = "custom";

    protected $fillable = ["from_id", "to_id", "type", "message", "is_read"];

    public function scopeSendRequestType($query)
    {
        return $query->where("type", static::SEND_REQUEST);
    }

    public function scopeAcceptRequestType($query)
    {
        return $query->where("type", static::ACCEPT_REQUEST);
    }

    public function scopeRead($query, $is_read=true)
    {
        return $query->where("is_read", $is_read);
    }

    public function getMessage()
    {
        switch ($this->type)
        {
            case static::SEND_REQUEST:
                $from = User::find($this->from_id);
                if (!is_null($from))
                {
                    return $from->getFullName() . " send request to you.";
                }
                else
                {
                    Log::error("Error getting notification message: User id " . $this->from_id . " is not exist.");
                }

            case static::ACCEPT_REQUEST:
                $to = User::find($this->to_id);

                if (!is_null($to))
                {
                    return $to->getFullName() . " accepted your request.";
                }
                else
                {
                    Log::error("Error getting notification message: User id " . $this->from_id . " is not exist.");
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
        return static::where(function($query) use($user) {
                    return $query->sendRequestType()->where('to_id', $user->getId());
                })
                ->orWhere(function($query) use($user) {
                    return $query->acceptRequestType()->where('from_id', $user->getId());
                });
    }

    public static function createSendRequest($from_id, $to_id)
    {
        $to = User::find($to_id);

        return static::create([
            'from_id' => $from_id,
            'to_id' => $to_id,
            'type' => static::SEND_REQUEST,
        ]);
    }
}
