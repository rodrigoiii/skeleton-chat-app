<?php

namespace Core;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Return model id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
