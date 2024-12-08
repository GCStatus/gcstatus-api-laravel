<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Rewardable;
use App\Http\Resources\RewardableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class RewardableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for RewardableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'sourceable' => 'object',
        'rewardable' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<RewardableResource>
     */
    public function resource(): string
    {
        return RewardableResource::class;
    }

    /**
     * Provide a mock instance of Rewardable for testing.
     *
     * @return \App\Models\Rewardable
     */
    public function modelInstance(): Rewardable
    {
        $rewardableMock = Mockery::mock(Rewardable::class)->makePartial();
        $rewardableMock->shouldAllowMockingMethod('getAttribute');

        $rewardableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\Rewardable $rewardableMock */
        return $rewardableMock;
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
