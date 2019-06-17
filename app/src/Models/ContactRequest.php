<?php

namespace App\Models;

use Core\BaseModel;

class ContactRequest extends BaseModel
{
    protected $fillable = ["by_id", "to_id", "is_read_by", "is_read_to", "is_accepted"];
}
