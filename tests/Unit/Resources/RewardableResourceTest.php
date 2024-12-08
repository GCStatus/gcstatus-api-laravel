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

        $this->assertEquals(new JsonResource([]), $array['sourceable']);
        $this->assertEquals(new JsonResource([]), $array['rewardable']);
    }

    /**
     * Test if can get resource for mission.
     *
     * @return void
     */
    public function test_if_can_get_resource_for_mission(): void
    {
        $missionMock = Mockery::mock(Mission::class)->makePartial();
        $missionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $missionMock->shouldReceive('toArray')->andReturn([
            'id' => 1,
            'coins' => null,
            'mission' => null,
            'for_all' => null,
            'frequency' => null,
            'experience' => null,
            'description' => null,
        ]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('sourceable', $missionMock);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $this->assertEquals(
            MissionResource::make($missionMock)->resolve(),
            $array['sourceable']
        );
    }

    /**
     * Test if can get resource for title.
     *
     * @return void
     */
    public function test_get_resource_for_title(): void
    {
        $titleMock = Mockery::mock(Title::class)->makePartial();
        $titleMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $titleMock->shouldReceive('toArray')->andReturn([
            'id' => 1,
            'cost' => null,
            'own' => true,
            'purchasable' => null,
            'description' => null,
            'created_at' => null,
            'updated_at' => null,
        ]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('rewardable', $titleMock);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $ownershipServiceMock = Mockery::mock(TitleOwnershipServiceInterface::class);
        $ownershipServiceMock
            ->shouldReceive('isOwnedByCurrentUser')
            ->andReturnTrue();

        /** @var \App\Contracts\Services\TitleOwnershipServiceInterface $ownershipServiceMock */
        TitleResource::setTitleOwnershipService($ownershipServiceMock);

        $this->assertEquals(
            TitleResource::make($titleMock)->resolve(),
            $array['rewardable'],
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
     * Test get getResourceForType for mission directly.
     *
     * @return void
     */
    public function test_get_getResourceForType_for_mission_correctly(): void
    {
        $missionMock = Mockery::mock(Mission::class)->makePartial();
        $missionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $missionMock->shouldReceive('toArray')->andReturn([
            'id' => 1,
            'coins' => null,
            'mission' => null,
            'for_all' => null,
            'frequency' => null,
            'experience' => null,
            'description' => null,
        ]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('sourceable', $missionMock);

        $resource = new RewardableResource($rewardable);

        $result = $resource->getResourceForType($rewardable->sourceable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $expected = MissionResource::make($missionMock)->toArray($request);

        unset($result['status'], $expected['status']);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test get getResourceForType for title directly.
     *
     * @return void
     */
    public function test_get_getResourceForType_for_title_correctly(): void
    {
        $titleMock = Mockery::mock(Title::class)->makePartial();
        $titleMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $titleMock->shouldReceive('toArray')->andReturn([
            'id' => 1,
            'cost' => null,
            'own' => true,
            'purchasable' => null,
            'description' => null,
            'created_at' => null,
            'updated_at' => null,
        ]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('rewardable', $titleMock);

        $resource = new RewardableResource($rewardable);

        $result = $resource->getResourceForType($rewardable->rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $expected = TitleResource::make($titleMock)->toArray($request);

        unset($result['users'], $expected['users']);
        unset($result['status'], $expected['status']);
        unset($result['rewardable'], $expected['rewardable']);

        $this->assertEquals($expected, $result);
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
