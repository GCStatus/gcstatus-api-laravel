<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Genreable;
use App\Contracts\Repositories\GenreableRepositoryInterface;

class GenreableRepositoryTest extends TestCase
{
    /**
     * The Genreable repository.
     *
     * @var \App\Contracts\Repositories\GenreableRepositoryInterface
     */
    private GenreableRepositoryInterface $genreableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genreableRepository = app(GenreableRepositoryInterface::class);
    }

    /**
     * Test if GenreableRepository uses the Genreable model correctly.
     *
     * @return void
     */
    public function test_Genreable_repository_uses_Genreable_model(): void
    {
        /** @var \App\Repositories\GenreableRepository $genreableRepository */
        $genreableRepository = $this->genreableRepository;

        $this->assertInstanceOf(Genreable::class, $genreableRepository->model());
    }
}
