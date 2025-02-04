<?php

namespace App\Services;

use App\Models\Commentable;
use App\Contracts\Repositories\CommentableRepositoryInterface;
use App\Exceptions\Commentable\CommentDoesntBelongsToUserException;
use App\Contracts\Services\{
    AuthServiceInterface,
    CommentableServiceInterface,
    ReplyNotificationServiceInterface,
};

class CommentableService extends AbstractService implements CommentableServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The reply notification service.
     *
     * @var \App\Contracts\Services\ReplyNotificationServiceInterface
     */
    private ReplyNotificationServiceInterface $replyNotificationService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->replyNotificationService = app(ReplyNotificationServiceInterface::class);
    }

    /**
     * The commentable repository.
     *
     * @return \App\Contracts\Repositories\CommentableRepositoryInterface
     */
    public function repository(): CommentableRepositoryInterface
    {
        return app(CommentableRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Commentable
    {
        $userId = $this->authService->getAuthId();

        /** @var \App\Models\Commentable @comment */
        $comment =  $this->repository()->create($data + [
            'user_id' => $userId,
        ]);

        $this->notifyNewReply($userId, $comment);

        return $comment;
    }

    /**
     * @inheritDoc
     */
    public function delete(mixed $id): void
    {
        $repository = $this->repository();

        /** @var \App\Models\Commentable $commentable */
        $commentable = $repository->findOrFail($id);

        $this->assertCanDelete($commentable);

        $commentable->delete();
    }

    /**
     * Assert can delete a comment.
     *
     * @param \App\Models\Commentable $commentable
     * @throws \App\Exceptions\Commentable\CommentDoesntBelongsToUserException
     * @return void
     */
    private function assertCanDelete(Commentable $commentable): void
    {
        $userId = $this->authService->getAuthId();

        if ($userId != $commentable->user_id) {
            throw new CommentDoesntBelongsToUserException();
        }
    }

    /**
     * Notify new reply.
     *
     * @param mixed $userId
     * @param \App\Models\Commentable $comment
     * @return void
     */
    private function notifyNewReply(mixed $userId, Commentable $comment): void
    {
        /** @var ?\App\Models\Commentable $parent */
        $parent = $comment->parent;

        if ($parent && $userId != $parent->user_id) {
            /** @var \App\Models\User $receiver */
            $receiver = $parent->user;

            /** @var \App\Models\User $replier */
            $replier = $comment->user;

            $this->replyNotificationService->notifyNewReply($receiver, $replier, $comment);
        }
    }
}
