<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\TransactionTypeRepository;
use App\Contracts\Services\TransactionTypeServiceInterface;
use App\Contracts\Repositories\TransactionTypeRepositoryInterface;

class TransactionTypeServiceTest extends TestCase
{
    /**
     * Test if TransactionTypeService uses the TorrentProvider repository correctly.
     *
     * @return void
     */
    public function test_TransactionTypeService_repository_uses_TorrentProvider_repository(): void
    {
        $this->app->instance(TransactionTypeRepositoryInterface::class, new TransactionTypeRepository());

        /** @var \App\Services\TransactionTypeService $transactionTypeService */
        $transactionTypeService = app(TransactionTypeServiceInterface::class);

        $this->assertInstanceOf(TransactionTypeRepository::class, $transactionTypeService->repository());
    }
}
