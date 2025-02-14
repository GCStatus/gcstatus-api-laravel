<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Repositories\LanguageRepository;
use App\Contracts\Services\LanguageServiceInterface;
use App\Contracts\Repositories\LanguageRepositoryInterface;

class LanguageServiceTest extends TestCase
{
    /**
     * The language service.
     *
     * @var \App\Contracts\Services\LanguageServiceInterface
     */
    private LanguageServiceInterface $languageService;

    /**
     * The language repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $languageRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->languageRepository = Mockery::mock(LanguageRepositoryInterface::class);

        $this->app->instance(LanguageRepositoryInterface::class, $this->languageRepository);

        $this->languageService = app(LanguageServiceInterface::class);
    }

    /**
     * Test if LanguageService uses the Language repository correctly.
     *
     * @return void
     */
    public function test_Language_service_uses_Language_repository(): void
    {
        $this->app->instance(LanguageRepositoryInterface::class, new LanguageRepository());

        /** @var \App\Services\LanguageService $languageService */
        $languageService = app(LanguageServiceInterface::class);

        $this->assertInstanceOf(LanguageRepository::class, $languageService->repository());
    }

    /**
     * Test if can find a language by name.
     *
     * @return void
     */
    public function test_if_can_find_a_language_by_name(): void
    {
        $name = fake()->name();

        $this->languageRepository
            ->shouldReceive('existsByName')
            ->once()
            ->with($name)
            ->andReturnTrue();

        $result = $this->languageService->existsByName($name);

        $this->assertTrue($result);
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
