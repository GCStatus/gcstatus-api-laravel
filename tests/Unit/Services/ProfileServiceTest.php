<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Profile};
use App\Services\ProfileService;
use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Repositories\ProfileRepositoryInterface;

class ProfileServiceTest extends TestCase
{
    /**
     * The social account repository.
     *
     * @var \App\Contracts\Services\ProfileServiceInterface
     */
    private ProfileServiceInterface $profileService;

    /**
     * The mock profile repository.
     *
     * @var \Mockery\MockInterface
     */
    private $mockRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(ProfileRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\ProfileRepositoryInterface $profileRepository */
        $profileRepository = $this->mockRepository;
        $this->profileService = new ProfileService($profileRepository);
    }

    /**
     * Test if it can correctly update a profile for a given user.
     *
     * @return void
     */
    public function test_if_can_correctly_update_profile_for_user(): void
    {
        $data = [
            'share' => fake()->boolean(),
            'photo' => fake()->imageUrl(),
        ];

        $userMock = Mockery::mock(User::class)->makePartial();

        $profileMock = Mockery::mock(Profile::class)->makePartial();
        $profileMock->shouldAllowMockingMethod('setAttribute');

        $profileMock->shouldReceive('getAttribute')->with('share')->andReturn($data['share']);
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturn($data['photo']);

        $this->mockRepository
            ->shouldReceive('updateForUser')
            ->once()
            ->with($userMock, $data)
            ->andReturn($profileMock);

        /** @var \App\Models\User $userMock */
        $result = $this->profileService->updateForUser($userMock, $data);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals($profileMock, $result);
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
