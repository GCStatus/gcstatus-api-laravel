<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\TransactionType;
use App\Contracts\Repositories\TransactionTypeRepositoryInterface;

class TransactionTypeRepositoryTest extends TestCase
{
    /**
     * The TransactionType repository.
     *
     * @var \App\Contracts\Repositories\TransactionTypeRepositoryInterface
     */
    private TransactionTypeRepositoryInterface $transactionTypeRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->transactionTypeRepository = app(TransactionTypeRepositoryInterface::class);
    }

    /**
     * Test if TransactionTypeRepository uses the TransactionType model correctly.
     *
     * @return void
     */
    public function test_TransactionType_repository_uses_TransactionType_model(): void
    {
        /** @var \App\Repositories\TransactionTypeRepository $transactionTypeRepository */
        $transactionTypeRepository = $this->transactionTypeRepository;

        $this->assertInstanceOf(TransactionType::class, $transactionTypeRepository->model());
    }
}
