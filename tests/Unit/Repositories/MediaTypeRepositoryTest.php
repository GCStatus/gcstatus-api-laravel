<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\MediaType;
use App\Contracts\Repositories\MediaTypeRepositoryInterface;

class MediaTypeRepositoryTest extends TestCase
{
    /**
     * The MediaType repository.
     *
     * @var \App\Contracts\Repositories\MediaTypeRepositoryInterface
     */
    private MediaTypeRepositoryInterface $mediaTypeRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mediaTypeRepository = app(MediaTypeRepositoryInterface::class);
    }

    /**
     * Test if MediaTypeRepository uses the MediaType model correctly.
     *
     * @return void
     */
    public function test_MediaType_repository_uses_MediaType_model(): void
    {
        /** @var \App\Repositories\MediaTypeRepository $mediaTypeRepository */
        $mediaTypeRepository = $this->mediaTypeRepository;

        $this->assertInstanceOf(MediaType::class, $mediaTypeRepository->model());
    }
}
