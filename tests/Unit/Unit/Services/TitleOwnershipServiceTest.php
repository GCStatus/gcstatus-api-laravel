<?php

namespace Tests\Unit\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Title;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Contracts\Services\{
    AuthServiceInterface,
    TitleOwnershipServiceInterface,
};

class TitleOwnershipServiceTest extends TestCase
{
    /**
     * The auth service mock.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The title ownership service.
     *
     * @var \App\Contracts\Services\TitleOwnershipServiceInterface
     */
    private TitleOwnershipServiceInterface $titleOwnershipService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);

        $this->titleOwnershipService = app(TitleOwnershipServiceInterface::class);
    }

    /**
     * Test if is owned by current user returns true when user owns title.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_is_owned_by_current_user_returns_true_when_user_owns_title(): void
    {
        $userId = 1;
        $titleId = 1;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $titleMock = Mockery::mock('overload:' . Title::class);

        $titleMock
            ->shouldReceive('whereIn')
            ->once()
            ->with('id', [$titleId])
            ->andReturnSelf();
        $titleMock
            ->shouldReceive('whereHas')
            ->once()
            ->andReturnSelf();
        $titleMock
            ->shouldReceive('pluck')
            ->once()
            ->andReturn(collect([$titleId]));

        /** @var \App\Models\Title $titleMock */
        $titleMock->id = $titleId;
        $result = $this->titleOwnershipService->isOwnedByCurrentUser($titleMock);

        $this->assertTrue($result);
        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if is owned by current user returns false when user doesn't own title.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_is_owned_by_current_user_returns_false_when_user_does_not_own_title(): void
    {
        $userId = 2;
        $titleId = 1;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $titleMock = Mockery::mock('overload:' . Title::class);

        $titleMock
            ->shouldReceive('whereIn')
            ->once()
            ->with('id', [$titleId])
            ->andReturnSelf();
        $titleMock
            ->shouldReceive('whereHas')
            ->once()
            ->andReturnSelf();
        $titleMock
            ->shouldReceive('pluck')
            ->once()
            ->andReturn(collect([]));

        /** @var \App\Models\Title $titleMock */
        $titleMock->id = $titleId;
        $result = $this->titleOwnershipService->isOwnedByCurrentUser($titleMock);

        $this->assertFalse($result);
        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
