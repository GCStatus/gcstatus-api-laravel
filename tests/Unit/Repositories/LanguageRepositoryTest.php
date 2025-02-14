<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Language;
use App\Repositories\LanguageRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Repositories\LanguageRepositoryInterface;

class LanguageRepositoryTest extends TestCase
{
    /**
     * The language repository.
     *
     * @var \App\Contracts\Repositories\LanguageRepositoryInterface
     */
    private LanguageRepositoryInterface $languageRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->languageRepository = app(LanguageRepositoryInterface::class);
    }

    /**
     * Test if LanguageRepository uses the Language model correctly.
     *
     * @return void
     */
    public function test_Language_repository_uses_Language_model(): void
    {
        /** @var \App\Repositories\LanguageRepository $languageRepository */
        $languageRepository = $this->languageRepository;

        $this->assertInstanceOf(Language::class, $languageRepository->model());
    }

    /**
     * Test if can check if language exists by name.
     *
     * @return void
     */
    public function test_if_can_check_if_language_exists_by_name(): void
    {
        $name = fake()->name();

        $builder = Mockery::mock(Builder::class);
        $language = Mockery::mock(Language::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', $name)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $language
            ->shouldReceive('query')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $repoMock = Mockery::mock(LanguageRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($language);

        /** @var \App\Contracts\Repositories\LanguageRepositoryInterface $repoMock */
        $repoMock->existsByName($name);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
