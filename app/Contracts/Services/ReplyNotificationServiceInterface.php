<?php

namespace App\Contracts\Services;

use App\Models\{User, Commentable};

interface ReplyNotificationServiceInterface
{
    /**
     * Notify given user from new reply on self comment.
     *
     * @param \App\Models\User $receiver
     * @param \App\Models\User $replier
     * @param \App\Models\Commentable $comment
     * @return void
     */
    public function notifyNewReply(User $receiver, User $replier, Commentable $comment): void;
}
