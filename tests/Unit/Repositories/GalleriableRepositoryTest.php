<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Galleriable;
use App\Contracts\Repositories\GalleriableRepositoryInterface;

class GalleriableRepositoryTest extends TestCase
{
    /**
     * The Galleriable repository.
     *
     * @var \App\Contracts\Repositories\GalleriableRepositoryInterface
     */
    private GalleriableRepositoryInterface $galleriableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->galleriableRepository = app(GalleriableRepositoryInterface::class);
    }

    /**
     * Test if GalleriableRepository uses the Galleriable model correctly.
     *
     * @return void
     */
    public function test_Galleriable_repository_uses_Galleriable_model(): void
    {
        /** @var \App\Repositories\GalleriableRepository $galleriableRepository */
        $galleriableRepository = $this->galleriableRepository;

        $this->assertInstanceOf(Galleriable::class, $galleriableRepository->model());
    }
}
