<?php

namespace App\Transformers;

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
        return [
            'id' => $user->getId(),
            'picture' => $user->getPicture(true),
            'full_name' => $user->getFullName(),
            'is_friend' => $this->searcher->isFriend($user),
            'has_pending_request' => $this->searcher->hasPendingRequestTo($user)
        ];
    }
}
