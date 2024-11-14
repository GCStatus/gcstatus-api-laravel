<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Profile};
use App\Contracts\Repositories\ProfileRepositoryInterface;

class ProfileRepositoryTest extends TestCase
{
    /**
     * The social account repository.
     *
     * @var \App\Contracts\Repositories\ProfileRepositoryInterface
     */
    private ProfileRepositoryInterface $profileRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->profileRepository = app(ProfileRepositoryInterface::class);
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

        $userMock->shouldReceive('getAttribute')
            ->with('profile')
            ->andReturn($profileMock);

        $profileMock->shouldReceive('update')
            ->once()
            ->with($data)
            ->andReturn(true);

        $profileMock->shouldReceive('fresh')
            ->once()
            ->andReturn($profileMock);

        /** @var \App\Models\User $userMock */
        $result = $this->profileRepository->updateForUser($userMock, $data);

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
