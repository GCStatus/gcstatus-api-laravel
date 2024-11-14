<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\SocialScope;
use App\Services\SocialScopeService;
use App\Contracts\Services\SocialScopeServiceInterface;
use App\Contracts\Repositories\SocialScopeRepositoryInterface;

class SocialScopeServiceTest extends TestCase
{
    /**
     * The social scope service.
     *
     * @var \App\Contracts\Services\SocialScopeServiceInterface
     */
    private SocialScopeServiceInterface $socialScopeService;

    /**
     * The social scope repository mock interface.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(SocialScopeRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\SocialScopeRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $this->socialScopeService = new SocialScopeService($mockRepository);
    }

    /**
     * Test if can correctly get the first or create social account.
     *
     * @return void
     */
    public function test_if_can_correctly_get_the_first_or_create_social_account(): void
    {
        $searchable = [
            'user_id' => 1,
        ];

        $creatable = [
            'provider' => 'github',
            'provider_id' => '123456',
        ];

        $socialScopeMock = Mockery::mock(SocialScope::class)->makePartial();
        $socialScopeMock->shouldAllowMockingMethod('setAttribute');

        $socialScopeMock->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $socialScopeMock->shouldReceive('getAttribute')->with('provider')->andReturn('google');
        $socialScopeMock->shouldReceive('getAttribute')->with('provider_id')->andReturn('123456');

        $this->mockRepository
            ->shouldReceive('firstOrCreate')
            ->with($searchable, $creatable)
            ->once()
            ->andReturn($socialScopeMock);

        $result = $this->socialScopeService->firstOrCreate($searchable, $creatable);

        $this->assertInstanceOf(SocialScope::class, $result);
        $this->assertEquals($socialScopeMock, $result);
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
