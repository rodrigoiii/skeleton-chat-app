<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $fillable = ["first_name", "last_name", "email", "password"];
}
