<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Publisherable;
use App\Contracts\Repositories\PublisherableRepositoryInterface;

class PublisherableRepositoryTest extends TestCase
{
    /**
     * The Publisherable repository.
     *
     * @var \App\Contracts\Repositories\PublisherableRepositoryInterface
     */
    private PublisherableRepositoryInterface $publisherableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->publisherableRepository = app(PublisherableRepositoryInterface::class);
    }

    /**
     * Test if PublisherableRepository uses the Publisherable model correctly.
     *
     * @return void
     */
    public function test_Publisherable_repository_uses_Publisherable_model(): void
    {
        /** @var \App\Repositories\PublisherableRepository $publisherableRepository */
        $publisherableRepository = $this->publisherableRepository;

        $this->assertInstanceOf(Publisherable::class, $publisherableRepository->model());
    }
}
