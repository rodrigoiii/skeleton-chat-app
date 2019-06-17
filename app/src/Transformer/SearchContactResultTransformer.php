<?php

namespace App\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class SearchContactResultTransformer extends TransformerAbstract
{
    private $searcher;

    public function __construct(User $searcher)
    {
        $this->searcher = $searcher;
    }

    /**
     * [transform description]
     *
     * @param  User $user
     * @return array
     */
    public function transform(User $user)
    {
        $has_pending_request = !is_null($this->searcher->requestTo($user));

        return [
            'id' => $user->getId(),
            'picture' => $user->getPicture(true),
            'full_name' => $user->getFullName(),
            'has_pending_request' => $has_pending_request
        ];
    }
}
