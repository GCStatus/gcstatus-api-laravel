<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Repositories\RequirementableRepository;
use App\Models\{Game, Requirementable, RequirementType};
use App\Contracts\Repositories\RequirementableRepositoryInterface;
use App\Contracts\Services\{
    RequirementTypeServiceInterface,
    RequirementableServiceInterface,
};

class RequirementableServiceTest extends TestCase
{
    /**
     * The requirement type service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $requirementTypeService;

    /**
     * The requirementable service.
     *
     * @var \App\Contracts\Services\RequirementableServiceInterface
     */
    private RequirementableServiceInterface $requirementableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requirementTypeService = Mockery::mock(RequirementTypeServiceInterface::class);

        $this->app->instance(RequirementTypeServiceInterface::class, $this->requirementTypeService);

        $this->requirementableService = app(RequirementableServiceInterface::class);
    }

    /**
     * Test if RequirementableService uses the Requirementable repository correctly.
     *
     * @return void
     */
    public function test_Requirementable_repository_uses_Requirementable_repository(): void
    {
        $this->app->instance(RequirementableRepositoryInterface::class, new RequirementableRepository());

        /** @var \App\Services\RequirementableService $requirementableService */
        $requirementableService = app(RequirementableServiceInterface::class);

        $this->assertInstanceOf(RequirementableRepository::class, $requirementableService->repository());
    }

    /**
     * Test if can create Requirementables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_Requirementables_for_steam_app(): void
    {
        $model = Mockery::mock(Game::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelRequirementType = Mockery::mock(RequirementType::class);
        $modelRequirementType->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->requirements = [ // @phpstan-ignore-line
            $requirement = [
                'os' => 'windows',
                'potential' => 'recommended',
                'attributes' => [
                    't_os' => 'Windows 10 64-bit',
                    'cpu' => 'Intel i5-4670k or AMD Ryzen 3 1200',
                    'ram' => '8 GB RAM',
                    'gpu' => 'NVIDIA GTX 1060 (6GB) or AMD RX 5500 XT (8GB) or Intel Arc A750',
                    'dx' => 'Version 12',
                    'storage' => '190 GB available space',
                    'obs' => 'Windows version 2004 2020-05-27 19041. 6GB GPU is required',
                ],
            ],
        ];

        $this->requirementTypeService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with([
                'os' => $requirement['os'],
                'potential' => $requirement['potential'],
            ])->andReturn($modelRequirementType);

        $repository = Mockery::mock(RequirementableRepositoryInterface::class);
        $this->app->instance(RequirementableRepositoryInterface::class, $repository);

        /** @var \App\Models\Game $model */
        /** @var \App\Models\RequirementType $modelRequirementType */
        $attributes = $requirement['attributes'];
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'network'             => 'N/A',
                'os'                  => $attributes['t_os'],
                'dx'                  => $attributes['dx'],
                'cpu'                 => $attributes['cpu'],
                'gpu'                 => $attributes['gpu'],
                'ram'                 => $attributes['ram'],
                'obs'                 => $attributes['obs'],
                'rom'                 => $attributes['storage'],
                'requirementable_id'  => $model->id,
                'requirementable_type' => $model::class,
                'requirement_type_id' => $modelRequirementType->id,
            ])->andReturn(Mockery::mock(Requirementable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        $this->requirementableService->createGameRequirements($model, $dto);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
