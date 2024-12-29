<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Title, UserTitle};
use App\Repositories\UserTitleRepository;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use App\Contracts\Repositories\UserTitleRepositoryInterface;

class UserTitleRepositoryTest extends TestCase
{
    /**
     * The user title repository.
     *
     * @var \App\Contracts\Repositories\UserTitleRepositoryInterface
     */
    private UserTitleRepositoryInterface $userTitleRepository;

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

    /**
     * Test if can toggle a title for given user.
     *
     * @return void
     */
    public function test_if_can_toggle_a_title_for_given_user(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $builder = Mockery::mock(Builder::class);
        $grammar = Mockery::mock(Grammar::class);
        $userTitle = Mockery::mock(UserTitle::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('user_id', $userId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($arg) use ($titleId, $grammar) {
                /** @var string $expectable */
                /** @var \Illuminate\Database\Query\Grammars\Grammar $grammar */
                $expectable = $arg['enabled']->getValue($grammar);

                return isset($arg['enabled']) &&
                    $arg['enabled'] instanceof Expression &&
                    str_contains($expectable, "CASE WHEN title_id = $titleId");
            }))->andReturnTrue();

        $userTitle->shouldReceive('query')->once()->andReturn($builder);

        $repoMock = Mockery::mock(UserTitleRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($userTitle);

        /** @var \App\Contracts\Repositories\UserTitleRepositoryInterface $repoMock */
        $repoMock->toggleTitle($userId, $titleId);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
