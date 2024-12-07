<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserTitleRepositoryInterface;
use Mockery;
use Tests\TestCase;
use App\Models\{User, Title, UserTitle};
use App\Repositories\UserTitleRepository;
use App\Contracts\Services\UserTitleServiceInterface;
use App\Exceptions\UserTitle\UserAlreadyHasGivenUserTitleException;
use App\Services\UserTitleService;
use Illuminate\Database\Eloquent\Collection;

class UserTitleServiceTest extends TestCase
{
    /**
     * The user title service.
     *
     * @var \App\Contracts\Services\UserTitleServiceInterface
     */
    private $userTitleService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userTitleService = app(UserTitleServiceInterface::class);
    }

    /**
     * Test if UserTitleService uses the UserTitle model correctly.
     *
     * @return void
     */
    public function test_user_title_repository_uses_user_title_model(): void
    {
        /** @var \App\Services\UserTitleService $userTitleService */
        $userTitleService = $this->userTitleService;

        $this->assertInstanceOf(UserTitleRepository::class, $userTitleService->repository());
    }

    /**
     * Test if can't assign title to user and throws exception if user already has title.
     *
     * @return void
     */
    public function test_if_cant_assign_title_to_user_and_throws_exception_if_user_already_has_title(): void
    {
        $titleId = 1;

        $user = Mockery::mock(User::class)->makePartial();
        $title = Mockery::mock(Title::class);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make([
                (object)['id' => $titleId],
            ]));

        $this->expectException(UserAlreadyHasGivenUserTitleException::class);
        $this->expectExceptionMessage('The user already has the given title.');

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        $this->userTitleService->assignTitleToUser($user, $title);
    }

    /**
     * Test if can assign title to user if not exists.
     *
     * @return void
     */
    public function test_if_can_assign_title_to_user_if_not_exists(): void
    {
        $userId = 1;
        $titleId = 2;

        $user = Mockery::mock(User::class)->makePartial();
        $title = Mockery::mock(Title::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make());

        $service->shouldReceive('repository')->andReturn($repository);

        $repository->shouldReceive('create')
            ->with(['user_id' => $userId, 'title_id' => $titleId])
            ->andReturn(Mockery::mock(UserTitle::class));

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $result = $service->assignTitleToUser($user, $title);

        $this->assertInstanceOf(UserTitle::class, $result);
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
