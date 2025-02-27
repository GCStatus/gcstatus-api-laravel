<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{Game, Languageable};
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\LanguageableRepository;
use App\Contracts\Repositories\LanguageableRepositoryInterface;

class LanguageableRepositoryTest extends TestCase
{
    /**
     * The Languageable repository.
     *
     * @var \App\Contracts\Repositories\LanguageableRepositoryInterface
     */
    private LanguageableRepositoryInterface $languageableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->languageableRepository = app(LanguageableRepositoryInterface::class);
    }

    /**
     * Test if LanguageableRepository uses the Languageable model correctly.
     *
     * @return void
     */
    public function test_Languageable_repository_uses_Languageable_model(): void
    {
        /** @var \App\Repositories\LanguageableRepository $languageableRepository */
        $languageableRepository = $this->languageableRepository;

        $this->assertInstanceOf(Languageable::class, $languageableRepository->model());
    }

    /**
     * Test if can check if languageable exists by payload.
     *
     * @return void
     */
    public function test_if_can_check_if_languageable_exists_by_payload(): void
    {
        $data = [
            'language_id' => 1,
            'languageable_id' => 1,
            'languageable_type' => Game::class,
        ];

        $builder = Mockery::mock(Builder::class);
        $languageable = Mockery::mock(Languageable::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('language_id', $data['language_id'])
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('languageable_id', $data['languageable_id'])
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('languageable_type', $data['languageable_type'])
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $languageable
            ->shouldReceive('query')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $repoMock = Mockery::mock(LanguageableRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($languageable);

        /** @var \App\Contracts\Repositories\LanguageableRepositoryInterface $repoMock */
        $repoMock->existsForPayload($data);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
