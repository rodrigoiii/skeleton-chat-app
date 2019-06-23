<?php

namespace App\Models;

use Core\BaseModel;

class Message extends BaseModel
{
    protected $fillable = ["message", "is_read", "from_id", "to_id"];

    /**
     * Sender of message
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function from()
    {
        return $this->belongsTo(User::class, "from_id");
    }

    /**
     * Receiver of message
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function to()
    {
        return $this->belongsTo(User::class, "to_id");
    }

    public static function conversation(User $from, User $to)
    {
        return static::where(function($query) use($from, $to) {
            return $query->where("from_id", $from->getId())
                        ->where("to_id", $to->getId());
        })->orWhere(function($query) use($from, $to) {
            return $query->where("from_id", $to->getId())
                        ->where("to_id", $from->getId());
        });
    }
}
