<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\GenreRepository;
use App\Contracts\Services\GenreServiceInterface;
use App\Contracts\Repositories\GenreRepositoryInterface;

class GenreServiceTest extends TestCase
{
    /**
     * Test if GenreService uses the Genre repository correctly.
     *
     * @return void
     */
    public function test_Genre_repository_uses_Genre_repository(): void
    {
        $this->app->instance(GenreRepositoryInterface::class, new GenreRepository());

        /** @var \App\Services\GenreService $GenreService */
        $GenreService = app(GenreServiceInterface::class);

        $this->assertInstanceOf(GenreRepository::class, $GenreService->repository());
    }
}
