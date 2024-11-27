<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Profile};
use App\Services\ProfileService;
use Illuminate\Http\UploadedFile;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    ProfileServiceInterface,
    StorageServiceInterface,
};

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
     * The mock storage service.
     *
     * @var \Mockery\MockInterface
     */
    private $mockStorageService;

    /**
     * The mock auth service.
     *
     * @var \Mockery\MockInterface
     */
    private $mockAuthService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockAuthService = Mockery::mock(AuthServiceInterface::class);
        $this->mockRepository = Mockery::mock(ProfileRepositoryInterface::class);
        $this->mockStorageService = Mockery::mock(StorageServiceInterface::class);

        /** @var \App\Contracts\Services\StorageServiceInterface $mockStorageService */
        $mockStorageService = $this->mockStorageService;

        /** @var \App\Contracts\Services\AuthServiceInterface $mockAuthService */
        $mockAuthService = $this->mockAuthService;

        /** @var \App\Contracts\Repositories\ProfileRepositoryInterface $profileRepository */
        $profileRepository = $this->mockRepository;

        $this->profileService = new ProfileService($profileRepository, $mockStorageService, $mockAuthService);
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

        $this->mockAuthService
            ->shouldReceive('forgetAuthUserCache')
            ->once()
            ->with($userMock);

        /** @var \App\Models\User $userMock */
        $result = $this->profileService->updateForUser($userMock, $data);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals($profileMock, $result);
    }

    /**
     * Test if can correctly update a profile picture for a given user.
     *
     * @return void
     */
    public function test_if_can_correctly_update_a_profile_picture_for_a_given_user(): void
    {
        $nickname = fake()->userName();
        $file = UploadedFile::fake()->create('valid.jpg');

        $profileMock = Mockery::mock(Profile::class)->makePartial();
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturnNull();

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn($nickname);
        $userMock->shouldReceive('getAttribute')->with('profile')->andReturn($profileMock);

        $this->mockStorageService->shouldNotReceive('delete');

        $path = "profiles/{$nickname}_profile_picture.{$file->getClientOriginalExtension()}";
        $this->mockStorageService
            ->shouldReceive('createAs')
            ->once()
            ->with($file, 'profiles', "{$nickname}_profile_picture.{$file->getClientOriginalExtension()}")
            ->andReturn($path);

        $this->mockRepository
            ->shouldReceive('updateForUser')
            ->once()
            ->with($userMock, ['photo' => $path])
            ->andReturn($profileMock);

        $this->mockAuthService
            ->shouldReceive('forgetAuthUserCache')
            ->once()
            ->with($userMock);

        /** @var \App\Models\User $userMock */
        $this->profileService->updatePicture($userMock, $file);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can correctly update a profile picture and remove old picture for a given user.
     *
     * @return void
     */
    public function test_if_can_correctly_update_a_profile_picture_and_remove_old_picture_for_a_given_user(): void
    {
        $old = fake()->imageUrl();
        $nickname = fake()->userName();
        $file = UploadedFile::fake()->create('valid.jpg');

        $profileMock = Mockery::mock(Profile::class)->makePartial();
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturn($old);

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn($nickname);
        $userMock->shouldReceive('getAttribute')->with('profile')->andReturn($profileMock);

        $this->mockStorageService
            ->shouldReceive('delete')
            ->once()
            ->with($old);

        $path = "profiles/{$nickname}_profile_picture.{$file->getClientOriginalExtension()}";
        $this->mockStorageService
            ->shouldReceive('createAs')
            ->once()
            ->with($file, 'profiles', "{$nickname}_profile_picture.{$file->getClientOriginalExtension()}")
            ->andReturn($path);

        $this->mockRepository
            ->shouldReceive('updateForUser')
            ->once()
            ->with($userMock, ['photo' => $path])
            ->andReturn($profileMock);

        $this->mockAuthService
            ->shouldReceive('forgetAuthUserCache')
            ->once()
            ->with($userMock);

        /** @var \App\Models\User $userMock */
        $this->profileService->updatePicture($userMock, $file);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
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
