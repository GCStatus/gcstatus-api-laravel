<?php

namespace Tests\Unit\Resources;

use Mockery;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Title, Mission, Rewardable};
use Illuminate\Http\Resources\Json\JsonResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Contracts\Services\TitleOwnershipServiceInterface;
use App\Http\Resources\{TitleResource, RewardableResource, MissionResource};

class RewardableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for RewardableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'rewardable_type' => 'string',
        'sourceable_type' => 'string',
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
        $rewardableMock->shouldReceive('getAttribute')->with('rewardable_type')->andReturn(Title::class);
        $rewardableMock->shouldReceive('getAttribute')->with('sourceable_type')->andReturn(Mission::class);

        /** @var \App\Models\Rewardable $rewardableMock */
        return $rewardableMock;
    }

    /**
     * Test if get resource for null model.
     *
     * @return void
     */
    public function test_if_get_resource_for_null_model(): void
    {
        $rewardable = $this->modelInstance();

        $rewardable->setRelation('sourceable', null);
        $rewardable->setRelation('rewardable', null);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $this->assertNull($array['sourceable']);
        $this->assertNull($array['rewardable']);
    }

    /**
     * Test if getResourceForType handles null model.
     *
     * @return void
     */
    public function test_if_get_resource_for_type_handles_null_model(): void
    {
        $rewardable = $this->modelInstance();

        $resource = new RewardableResource($rewardable);

        $result = $resource->getResourceForType(null);

        $this->assertInstanceOf(JsonResource::class, $result);
        $this->assertEquals([], $result->resolve());
    }

    /**
     * Test if can get resource for mission.
     *
     * @return void
     */
    public function test_if_can_get_resource_for_mission(): void
    {
        $mission = new Mission();
        $mission->id = 1;

        $rewardable = new Rewardable();
        $rewardable->setRelation('sourceable', $mission);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $expected = MissionResource::make($mission)->resolve();

        /** @var non-empty-string $expected */
        $expected = json_encode($expected);

        /** @var non-empty-string $value */
        $value = json_encode($array['sourceable']);

        $this->assertEquals(
            json_decode($expected, true),
            json_decode($value, true)
        );
    }

    /**
     * Test if can get resource for title.
     *
     * @return void
     */
    public function test_get_resource_for_title(): void
    {
        $title = new Title();
        $title->id = 1;

        $rewardable = new Rewardable();
        $rewardable->setRelation('rewardable', $title);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $ownershipServiceMock = Mockery::mock(TitleOwnershipServiceInterface::class);
        $ownershipServiceMock
            ->shouldReceive('isOwnedByCurrentUser')
            ->andReturnTrue();

        $this->app->instance(TitleOwnershipServiceInterface::class, $ownershipServiceMock);

        $expected = TitleResource::make($title)->resolve();

        /** @var non-empty-string $expected */
        $expected = json_encode($expected);

        /** @var non-empty-string $value */
        $value = json_encode($array['rewardable']);

        $this->assertEquals(
            json_decode($expected, true),
            json_decode($value, true),
        );
    }

    /**
     * Test if can get resource for default case.
     *
     * @return void
     */
    public function test_if_can_get_resource_for_default_case(): void
    {
        $genericModel = Mockery::mock(Model::class)->makePartial();
        $genericModel->shouldReceive('toArray')->andReturn(['custom' => 'value']);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('sourceable', $genericModel);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $this->assertEquals(['custom' => 'value'], $array['sourceable']);
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
