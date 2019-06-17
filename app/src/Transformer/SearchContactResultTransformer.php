<?php

namespace App\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class SearchContactResultTransformer extends TransformerAbstract
{
    /**
     * [transform description]
     *
     * @param  User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'picture' => $user->getPicture(true),
            'full_name' => $user->getFullName()
        ];
    }
}
