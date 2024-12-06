<?php

namespace Tests\Unit\Strategies;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\User;
use App\Strategies\TitleRewardStrategy;
use App\Contracts\Services\UserTitleServiceInterface;
use App\Models\Rewardable;
use App\Models\Title;

class TitleRewardStrategyTest extends TestCase
{
    /**
     * The user title service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userTitleService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userTitleService = Mockery::mock(UserTitleServiceInterface::class);

        $this->app->instance(UserTitleServiceInterface::class, $this->userTitleService);
    }

    /**
     * Test if can award title to user.
     *
     * @return void
     */
    public function test_if_can_award_title_to_user(): void
    {
        $strategy = new TitleRewardStrategy();

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $rewardable = Mockery::mock(Rewardable::class);

        $rewardable->shouldReceive('getAttribute')->with('rewardable')->andReturn($title);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        /** @var \App\Models\Rewardable $rewardable */
        $this->userTitleService
            ->shouldReceive('assignTitleToUser')
            ->once()
            ->with($user, $title);

        $strategy->award($user, $rewardable);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down the tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
