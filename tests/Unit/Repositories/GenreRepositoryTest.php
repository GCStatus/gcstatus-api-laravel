<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Genre;
use App\Contracts\Repositories\GenreRepositoryInterface;

class GenreRepositoryTest extends TestCase
{
    /**
     * The Genre repository.
     *
     * @var \App\Contracts\Repositories\GenreRepositoryInterface
     */
    private GenreRepositoryInterface $genreRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genreRepository = app(GenreRepositoryInterface::class);
    }

    /**
     * Test if GenreRepository uses the Genre model correctly.
     *
     * @return void
     */
    public function test_Genre_repository_uses_Genre_model(): void
    {
        /** @var \App\Repositories\GenreRepository $genreRepository */
        $genreRepository = $this->genreRepository;

        $this->assertInstanceOf(Genre::class, $genreRepository->model());
    }
}
