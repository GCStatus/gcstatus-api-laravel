<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Banner, Game};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Http\Resources\{BannerResource, GameResource};

class BannerResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for BannerResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'type' => 'string',
        'bannerable' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<BannerResource>
     */
    public function resource(): string
    {
        return BannerResource::class;
    }

    /**
     * Provide a mock instance of Banner for testing.
     *
     * @return \App\Models\Banner
     */
    public function modelInstance(): Banner
    {
        $bannerMock = Mockery::mock(Banner::class)->makePartial();
        $bannerMock->shouldAllowMockingMethod('getAttribute');

        $bannerMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $bannerMock->shouldReceive('getAttribute')->with('bannerable_type')->andReturn(Game::class);

        /** @var \App\Models\Banner $bannerMock */
        return $bannerMock;
    }

    /**
     * Test if get resource for null model.
     *
     * @return void
     */
    public function test_if_get_resource_for_null_model(): void
    {
        $banner = $this->modelInstance();

        $banner->setRelation('bannerable', null);

        $resource = new BannerResource($banner);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $this->assertEquals(new JsonResource([]), $array['bannerable']);
    }

    /**
     * Test if getResourceForType handles null model.
     *
     * @return void
     */
    public function test_if_get_resource_for_type_handles_null_model(): void
    {
        $banner = $this->modelInstance();

        $resource = new BannerResource($banner);

        $result = $resource->getResourceForType(null);

        $this->assertInstanceOf(JsonResource::class, $result);
        $this->assertEquals([], $result->resolve());
    }

    /**
     * Test if can get resource for game.
     *
     * @return void
     */
    public function test_if_can_get_resource_for_game(): void
    {
        $game = new Game();
        $game->id = 1;

        $banner = new Banner();
        $banner->setRelation('bannerable', $game);

        $resource = new BannerResource($banner);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $expected = GameResource::make($game)->resolve();

        /** @var non-empty-string $expected */
        $expected = json_encode($expected);

        /** @var non-empty-string $value */
        $value = json_encode($array['bannerable']);

        $this->assertEquals(
            json_decode($expected, true),
            json_decode($value, true)
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

        $banner = $this->modelInstance();
        $banner->setRelation('bannerable', $genericModel);

        $resource = new BannerResource($banner);

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');

        $array = $resource->toArray($request);

        $this->assertEquals(['custom' => 'value'], $array['bannerable']);
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
