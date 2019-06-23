<?php

namespace App\Models;

use Core\BaseModel;

class Message extends BaseModel
{
    protected $fillable = ["message", "is_read", "from_id", "to_id"];
}
