<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Critic;
use App\Contracts\Repositories\CriticRepositoryInterface;

class CriticRepositoryTest extends TestCase
{
    /**
     * The Critic repository.
     *
     * @var \App\Contracts\Repositories\CriticRepositoryInterface
     */
    private CriticRepositoryInterface $criticRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->criticRepository = app(CriticRepositoryInterface::class);
    }

    /**
     * Test if CriticRepository uses the Critic model correctly.
     *
     * @return void
     */
    public function test_Critic_repository_uses_Critic_model(): void
    {
        /** @var \App\Repositories\CriticRepository $criticRepository */
        $criticRepository = $this->criticRepository;

        $this->assertInstanceOf(Critic::class, $criticRepository->model());
    }
}
