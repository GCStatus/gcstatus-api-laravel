<?php

namespace App\Services;

use App\Models\{User, Commentable, Game};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\ReplyNotificationServiceInterface;

class ReplyNotificationService implements ReplyNotificationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function notifyNewReply(User $receiver, User $replier, Commentable $comment): void
    {
        $notification = [
            'icon' => 'FaRegComment',
            'userId' => (string)$receiver->id,
            'actionUrl' => $this->generateActionUrl($comment),
            'title' => "$replier->nickname just replied your comment.",
        ];

        $receiver->notify(new DatabaseNotification($notification));
    }

    /**
     * Generate the action url for related commentable.
     *
     * @param \App\Models\Commentable $comment
     * @return string
     */
    private function generateActionUrl(Commentable $comment): string
    {
        $commentable = $comment->commentable;

        return match ($comment->commentable_type) {
            normalizeMorphAdmin(Game::class) => "/games/$commentable->slug",
            default => '#',
        };
    }
}
