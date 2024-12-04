<?php

namespace Tests\Unit\Strategies;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Models\{User, MissionRequirement};
use App\Strategies\TransactionCountStrategy;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCountStrategyTest extends TestCase
{
    /**
     * Test if can calculate the progress below the goal.
     *
     * @return void
     */
    public function test_if_can_calculate_the_progress_below_the_goal(): void
    {
        $strategy = new TransactionCountStrategy();

        $user = Mockery::mock(User::class);

        $requirement = new MissionRequirement(['goal' => 5, 'created_at' => now()->subWeek()]);

        $transactionsMock = Mockery::mock(HasMany::class);

        $transactionsMock
            ->shouldReceive('where')
            ->with('created_at', '>=', $requirement->created_at)
            ->andReturn($transactionsMock);

        $transactionsMock->shouldReceive('count')->andReturn(3);

        $user->shouldReceive('transactions')->andReturn($transactionsMock);

        /** @var \App\Models\User $user */
        $progress = $strategy->calculateProgress($user, $requirement);

        $this->assertEquals(3, $progress);
    }

    /**
     * Test if can calculate the progress that exceeds the goal.
     *
     * @return void
     */
    public function test_if_can_calculate_the_progress_that_exceeds_the_goal(): void
    {
        $strategy = new TransactionCountStrategy();

        $user = Mockery::mock(User::class);

        $requirement = new MissionRequirement(['goal' => 5, 'created_at' => now()->subWeek()]);

        $transactionsMock = Mockery::mock(HasMany::class);

        $transactionsMock
            ->shouldReceive('where')
            ->with('created_at', '>=', $requirement->created_at)
            ->andReturn($transactionsMock);

        $transactionsMock->shouldReceive('count')->andReturn(10);

        $user->shouldReceive('transactions')->andReturn($transactionsMock);

        /** @var \App\Models\User $user */
        $progress = $strategy->calculateProgress($user, $requirement);

        $this->assertEquals(5, $progress);
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
