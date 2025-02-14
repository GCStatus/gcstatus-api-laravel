<?php

namespace Tests\Unit\Services\Validation;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use InvalidArgumentException;
use App\Contracts\Services\LogServiceInterface;
use App\Contracts\Services\Validation\SteamAppValidatorInterface;

class SteamAppValidatorTest extends TestCase
{
    /**
     * The log service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $logService;

    /**
     * The steam app validator.
     *
     * @var \App\Contracts\Services\Validation\SteamAppValidatorInterface
     */
    private SteamAppValidatorInterface $steamAppValidator;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logService = Mockery::mock(LogServiceInterface::class);

        $this->app->instance(LogServiceInterface::class, $this->logService);

        $this->steamAppValidator = app(SteamAppValidatorInterface::class);
    }

    /**
     * Test if can throw an exception if missing required fields.
     *
     * @return void
     */
    public function test_if_can_throw_an_exception_if_missing_required_fields(): void
    {
        $data = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: type');

        $this->logService
            ->shouldReceive('withContext')
            ->with('Failed to validate steam app: ', [
                'data' => $data,
                'missing_field' => 'type',
            ])->once()
            ->andReturnNull();

        $this->steamAppValidator->validate($data);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can pass on valid data.
     *
     * @return void
     */
    public function test_if_can_pass_on_valid_data(): void
    {
        $data = [
            'type' => 'game',
            'name' => 'Half-Life',
            'is_free' => false,
            'steam_appid' => '182931',
            'release_date' => '28 Nov, 2019',
            'background_raw' => fake()->imageUrl(),
            'about_the_game' => fake()->realText(),
            'short_description' => fake()->realText(),
            'detailed_description' => fake()->realText(),
        ];

        $this->logService->shouldNotReceive('withContext');

        $this->steamAppValidator->validate($data);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
