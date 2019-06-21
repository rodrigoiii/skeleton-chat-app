<?php

namespace App\Models;

use Core\BaseModel;

class ChatStatus extends BaseModel
{
    const ONLINE = "online";
    const OFFLINE = "offline";

    protected $fillable = ["status", "user_id"];

    public function setAsOnline()
    {
        $this->status = static::ONLINE;
        return $this->save();
    }

    public function setAsOffline()
    {
        $this->status = static::OFFLINE;
        return $this->save();
    }

    public static function createOnlineUser(User $user)
    {
        return static::create([
            'status' => static::ONLINE,
            'user_id' => $user->getId()
        ]);
    }
}
