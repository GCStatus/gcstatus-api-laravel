<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\UserTitle;
use App\Contracts\Repositories\UserTitleRepositoryInterface;

class UserTitleRepositoryTest extends TestCase
{
    /**
     * The user title repository.
     *
     * @var \App\Contracts\Repositories\UserTitleRepositoryInterface
     */
    private $userTitleRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userTitleRepository = app(UserTitleRepositoryInterface::class);
    }

    /**
     * Test if UserTitleRepository uses the UserTitle model correctly.
     *
     * @return void
     */
    public function test_user_title_repository_uses_user_title_model(): void
    {
        /** @var \App\Repositories\UserTitleRepository $userTitleRepository */
        $userTitleRepository = $this->userTitleRepository;

        $this->assertInstanceOf(UserTitle::class, $userTitleRepository->model());
    }
}
