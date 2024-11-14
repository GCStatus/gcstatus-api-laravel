<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\SocialAccount;
use App\Services\SocialAccountService;
use App\Contracts\Services\SocialAccountServiceInterface;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

class SocialAccountServiceTest extends TestCase
{
    /**
     * The social account service.
     *
     * @var \App\Contracts\Services\SocialAccountServiceInterface
     */
    private SocialAccountServiceInterface $socialAccountService;

    /**
     * The social account repository mock interface.
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

        $this->mockRepository = Mockery::mock(SocialAccountRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\SocialAccountRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $this->socialAccountService = new SocialAccountService($mockRepository);
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

        $socialAccountMock = Mockery::mock(SocialAccount::class)->makePartial();
        $socialAccountMock->shouldAllowMockingMethod('setAttribute');

        $socialAccountMock->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $socialAccountMock->shouldReceive('getAttribute')->with('provider')->andReturn('google');
        $socialAccountMock->shouldReceive('getAttribute')->with('provider_id')->andReturn('123456');

        $this->mockRepository
            ->shouldReceive('firstOrCreate')
            ->with($searchable, $creatable)
            ->once()
            ->andReturn($socialAccountMock);

        $result = $this->socialAccountService->firstOrCreate($searchable, $creatable);

        $this->assertInstanceOf(SocialAccount::class, $result);
        $this->assertEquals($socialAccountMock, $result);
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
