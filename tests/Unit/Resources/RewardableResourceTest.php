<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\MissionResource;
use Mockery;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\RewardableResource;
use App\Http\Resources\TitleResource;
use App\Models\{Title, Mission, Rewardable};
use Illuminate\Http\Resources\Json\JsonResource;
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
        $missionMock->shouldReceive('toArray')->andReturn(['id' => 1]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('sourceable', $missionMock);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        /** @var array<string, mixed> $sourceable */
        $sourceable = $array['sourceable'];

        /** @var \App\Models\Mission $missionMock */
        $this->assertEquals($missionMock->toArray(), $sourceable);

        $this->assertInstanceOf(MissionResource::class, MissionResource::make($missionMock));
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
        $titleMock->shouldReceive('toArray')->andReturn(['id' => 1]);

        $rewardable = $this->modelInstance();
        $rewardable->setRelation('rewardable', $titleMock);

        $resource = new RewardableResource($rewardable);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        /** @var array<string, mixed> $rewardable */
        $rewardable = $array['rewardable'];

        /** @var \App\Models\Title $titleMock */
        $this->assertEquals($titleMock->toArray(), $rewardable);

        $this->assertInstanceOf(TitleResource::class, TitleResource::make($titleMock));
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
