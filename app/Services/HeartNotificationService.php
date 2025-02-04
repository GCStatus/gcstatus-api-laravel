<?php

namespace App\Services;

use InvalidArgumentException;
use App\Notifications\DatabaseNotification;
use App\Models\{User, Heartable, Commentable, Game};
use App\Contracts\Services\HeartNotificationServiceInterface;

class HeartNotificationService implements HeartNotificationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function notifyNewHeart(Heartable $heartable): void
    {
        if (!$this->assertCanNotify($heartable)) {
            return;
        }

        $notifiable = $this->getNotifiable($heartable);

        $notification = $this->getNotification($heartable, $notifiable);

        $notifiable->notify(new DatabaseNotification($notification));
    }

    /**
     * Get the notifiable user.
     *
     * @param \App\Models\Heartable $heartable
     * @return \App\Models\User
     */
    private function getNotifiable(Heartable $heartable): User
    {
        switch ($heartable->heartable_type) {
            case normalizeMorphAdmin(Commentable::class):
                /** @var \App\Models\Commentable $commentable */
                $commentable = $heartable->heartable;

                /** @var \App\Models\User */
                return $commentable->user;

            default:
                throw new InvalidArgumentException('Invalid heartable type for notification.');
        }
    }

    /**
     * Get notification details.
     *
     * @param \App\Models\Heartable $heartable
     * @param \App\Models\User $notifiable
     * @return array<string, string>
     */
    private function getNotification(Heartable $heartable, User $notifiable): array
    {
        switch ($heartable->heartable_type) {
            case normalizeMorphAdmin(Commentable::class):
                /** @var \App\Models\Commentable $commentable */
                $commentable = $heartable->heartable;

                /** @var \App\Models\User $hearter */
                $hearter = $heartable->user;

                return [
                    'icon' => 'IoIosHeartEmpty',
                    'userId' => (string)$notifiable->id,
                    'title' => "{$hearter->nickname} hearted your comment!",
                    'actionUrl' => $this->generateCommentActionUrl($commentable),
                ];

            default:
                throw new InvalidArgumentException('Invalid heartable type for notification.');
        }
    }

    /**
     * Generate the action URL for the commentable.
     *
     * @param \App\Models\Commentable $comment
     * @return string
     */
    private function generateCommentActionUrl(Commentable $comment): string
    {
        $commentable = $comment->commentable;

        return match ($comment->commentable_type) {
            normalizeMorphAdmin(Game::class) => "/games/{$commentable->slug}",
            default => '#',
        };
    }

    /**
     * Validate whether a notification should be sent.
     *
     * @param \App\Models\Heartable $heartable
     * @return bool
     */
    private function assertCanNotify(Heartable $heartable): bool
    {
        switch ($heartable->heartable_type) {
            case normalizeMorphAdmin(Commentable::class):
                /** @var \App\Models\Commentable $commentable */
                $commentable = $heartable->heartable;

                /** @var \App\Models\User $hearter */
                $hearter = $heartable->user;

                return $commentable->user_id != $hearter->id;

            default:
                return false;
        }
    }
}
