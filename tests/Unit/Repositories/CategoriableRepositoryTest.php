<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Categoriable;
use App\Contracts\Repositories\CategoriableRepositoryInterface;

class CategoriableRepositoryTest extends TestCase
{
    /**
     * The categoriable repository.
     *
     * @var \App\Contracts\Repositories\CategoriableRepositoryInterface
     */
    private CategoriableRepositoryInterface $categoriableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->categoriableRepository = app(CategoriableRepositoryInterface::class);
    }

    /**
     * Test if CategoriableRepository uses the Categoriable model correctly.
     *
     * @return void
     */
    public function test_categoriable_repository_uses_categoriable_model(): void
    {
        /** @var \App\Repositories\CategoriableRepository $categoriableRepository */
        $categoriableRepository = $this->categoriableRepository;

        $this->assertInstanceOf(Categoriable::class, $categoriableRepository->model());
    }
}
