<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Commentable;
use App\Contracts\Repositories\CommentableRepositoryInterface;

class CommentableRepositoryTest extends TestCase
{
    /**
     * The commentable repository.
     *
     * @var \App\Contracts\Repositories\CommentableRepositoryInterface
     */
    private CommentableRepositoryInterface $commentableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->commentableRepository = app(CommentableRepositoryInterface::class);
    }

    /**
     * Test if CommentableRepository uses the Commentable model correctly.
     *
     * @return void
     */
    public function test_commentable_repository_uses_commentable_model(): void
    {
        /** @var \App\Repositories\CommentableRepository $commentableRepository */
        $commentableRepository = $this->commentableRepository;

        $this->assertInstanceOf(Commentable::class, $commentableRepository->model());
    }
}
