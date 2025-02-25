<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Protection;
use App\Contracts\Repositories\ProtectionRepositoryInterface;

class protectionRepositoryTest extends TestCase
{
    /**
     * The protection repository.
     *
     * @var \App\Contracts\Repositories\ProtectionRepositoryInterface
     */
    private ProtectionRepositoryInterface $protectionRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->protectionRepository = app(ProtectionRepositoryInterface::class);
    }

    /**
     * Test if protectionRepository uses the protection model correctly.
     *
     * @return void
     */
    public function test_protection_repository_uses_protection_model(): void
    {
        /** @var \App\Repositories\ProtectionRepository $protectionRepository */
        $protectionRepository = $this->protectionRepository;

        $this->assertInstanceOf(Protection::class, $protectionRepository->model());
    }
}
