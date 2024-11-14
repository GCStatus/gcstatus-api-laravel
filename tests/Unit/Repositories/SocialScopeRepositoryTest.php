<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\SocialScope;
use App\Contracts\Repositories\SocialScopeRepositoryInterface;

class SocialScopeRepositoryTest extends TestCase
{
    /**
     * The social account repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $socialScopeRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->socialScopeRepository = Mockery::mock(SocialScopeRepositoryInterface::class);
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

        $this->socialScopeRepository
            ->shouldReceive('firstOrCreate')
            ->with($searchable, $creatable)
            ->once()
            ->andReturn($socialScopeMock);

        /** @var \App\Contracts\Repositories\SocialScopeRepositoryInterface $socialScopeRepository */
        $socialScopeRepository = $this->socialScopeRepository;
        $result = $socialScopeRepository->firstOrCreate($searchable, $creatable);

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
