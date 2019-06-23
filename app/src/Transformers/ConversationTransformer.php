<?php

namespace App\Transformers;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class ConversationTransformer extends TransformerAbstract
{
    /**
     * [transform description]
     *
     * @param  Message $message
     * @return array
     */
    public function transform(Message $message)
    {
        $sender = $message->from;
        // $receiver = $message->to;

        return [
            'message' => $message->message,
            'sender' => [
                'id' => $sender->id,
                'picture' => $sender->getPicture(true),
                'full_name' => $sender->getFullName(),
                'sent_at' => $message->created_at
            ]
        ];
    }
}
