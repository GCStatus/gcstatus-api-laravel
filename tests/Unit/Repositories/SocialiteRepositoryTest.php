<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Contracts\Repositories\SocialiteRepositoryInterface;

class SocialiteRepositoryTest extends TestCase
{
    /**
     * The socialite repository.
     *
     * @var \App\Contracts\Repositories\SocialiteRepositoryInterface
     */
    private $socialiteRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->socialiteRepository = app(SocialiteRepositoryInterface::class);
    }

    /**
     * Test if the redirect method generates the correct response.
     *
     * @return void
     */
    public function test_if_redirect_method_generates_correct_response(): void
    {
        $providerName = 'github';
        $state = 'testState';

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('with')
            ->with(['state' => $state, 'prompt' => 'select_account'])
            ->once()
            ->andReturnSelf();

        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();

        $redirectResponseMock = Mockery::mock(RedirectResponse::class);
        $providerMock->shouldReceive('redirect')
            ->once()
            ->andReturn($redirectResponseMock);

        Socialite::shouldReceive('driver')
            ->with($providerName)
            ->once()
            ->andReturn($providerMock);

        $result = $this->socialiteRepository->redirect($providerName, $state);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($redirectResponseMock, $result);
    }

    /**
     * Test if the getCallbackUser method retrieves a user correctly.
     *
     * @return void
     */
    public function test_if_the_get_callback_user_method_retrieves_a_user_correctly(): void
    {
        $providerName = 'github';

        $socialiteUserMock = Mockery::mock(SocialiteUser::class);
        $socialiteUserMock->shouldReceive('getId')->andReturn('12345');
        $socialiteUserMock->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUserMock->shouldReceive('getName')->andReturn('Test User');

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUserMock);

        Socialite::shouldReceive('driver')
            ->with($providerName)
            ->once()
            ->andReturn($providerMock);

        $result = $this->socialiteRepository->getCallbackUser($providerName);

        $this->assertInstanceOf(SocialiteUser::class, $result);
        $this->assertEquals('12345', $result->getId());
        $this->assertEquals('test@example.com', $result->getEmail());
        $this->assertEquals('Test User', $result->getName());
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
