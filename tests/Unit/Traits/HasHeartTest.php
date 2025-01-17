<?php

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use App\Models\Game;
use RuntimeException;
use App\Traits\HasHeart;
use Mockery\MockInterface;
use App\Contracts\Services\AuthServiceInterface;
use Illuminate\Database\Eloquent\{Model, Builder};

class HasHeartTest extends TestCase
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

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
    }

    /**
     * Test if can call is hearted attribute successfully.
     *
     * @return void
     */
    public function test_if_can_call_is_hearted_attribute_successfully(): void
    {
        $userId = 1;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive('withCount')
            ->once()
            ->with(Mockery::on(function (array $argument) {
                return isset($argument['hearts as is_hearted']) && is_callable($argument['hearts as is_hearted']);
            }))->andReturnSelf();

        $model = new Game();

        /** @var \Illuminate\Database\Eloquent\Builder<Game> $builderMock */
        $result = $model->scopeWithIsHearted($builderMock);

        $this->assertSame($builderMock, $result);
    }

    /**
     * Test that the trait throws an exception when the "hearts" relationship is missing.
     *
     * @return void
     */
    public function test_exception_when_missing_hearts_relationship(): void
    {
        $model = new class () extends Model {
            use HasHeart;

            public function save(array $options = []): bool
            {
                $this->fireModelEvent('retrieved', false);

                return true;
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'The model [%s] must implement HasHeartInterface to use the HasHeart trait.',
            get_class($model),
        ));

        $model->save();
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
