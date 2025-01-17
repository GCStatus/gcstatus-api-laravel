<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Title;
use App\Http\Resources\TitleResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Contracts\Services\TitleOwnershipServiceInterface;

class TitleResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TitleResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'cost' => 'int',
        'own' => 'bool',
        'title' => 'string',
        'status' => 'object',
        'purchasable' => 'bool',
        'rewardable' => 'object',
        'created_at' => 'string',
        'updated_at' => 'string',
        'description' => 'string',
        'users' => 'resourceCollection',
    ];

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $ownershipServiceMock = Mockery::mock(TitleOwnershipServiceInterface::class);
        $ownershipServiceMock
            ->shouldReceive('isOwnedByCurrentUser')
            ->andReturnTrue();

        $this->app->instance(TitleOwnershipServiceInterface::class, $ownershipServiceMock);
    }

    /**
     * Get the resource class being tested.
     *
     * @return class-string<TitleResource>
     */
    public function resource(): string
    {
        return TitleResource::class;
    }

    /**
     * Provide a mock instance of Title for testing.
     *
     * @return \App\Models\Title
     */
    public function modelInstance(): Title
    {
        $titleMock = Mockery::mock(Title::class)->makePartial();
        $titleMock->shouldAllowMockingMethod('getAttribute');

        $ownershipServiceMock = Mockery::mock(TitleOwnershipServiceInterface::class);
        $ownershipServiceMock
            ->shouldReceive('isOwnedByCurrentUser')
            ->andReturnTrue();

        $this->app->instance(TitleOwnershipServiceInterface::class, $ownershipServiceMock);

        $titleMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $titleMock->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $titleMock->shouldReceive('getAttribute')->with('own')->andReturn(fake()->boolean());
        $titleMock->shouldReceive('getAttribute')->with('purchasable')->andReturn(fake()->boolean());
        $titleMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->realText());
        $titleMock->shouldReceive('getAttribute')->with('created_at')->andReturn(now()->toISOString());
        $titleMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now()->toISOString());
        $titleMock->shouldReceive('getAttribute')->with('cost')->andReturn(fake()->numberBetween(1, 9999));

        /** @var \App\Models\Title $titleMock */
        return $titleMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
