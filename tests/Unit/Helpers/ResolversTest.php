<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Contracts\Services\{
    AwardServiceInterface,
    LogServiceInterface,
    CacheServiceInterface,
    StorageServiceInterface,
    TransactionServiceInterface,
    ProgressCalculatorServiceInterface,
};

class ResolversTest extends TestCase
{
    /**
     * Test if can get correct binded interface for storage service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_storage_service(): void
    {
        $resolvedStorage = storage();

        $this->assertInstanceOf(StorageServiceInterface::class, $resolvedStorage);
    }

    /**
     * Test if can get correct binded interface for cache service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_cache_service(): void
    {
        $resolvedCacher = cacher();

        $this->assertInstanceOf(CacheServiceInterface::class, $resolvedCacher);
    }

    /**
     * Test if can get correct binded interface for progress calculator service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_progress_calculator_service(): void
    {
        $resolvedProgressCalculator = progressCalculator();

        $this->assertInstanceOf(ProgressCalculatorServiceInterface::class, $resolvedProgressCalculator);
    }

    /**
     * Test if can get correct binded interface for log service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_log_service(): void
    {
        $resolvedLogService = logService();

        $this->assertInstanceOf(LogServiceInterface::class, $resolvedLogService);
    }

    /**
     * Test if can get correct binded interface for transaction service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_transaction_service(): void
    {
        $resolvedTransactionService = transactionService();

        $this->assertInstanceOf(TransactionServiceInterface::class, $resolvedTransactionService);
    }

    /**
     * Test if can get correct binded interface for award service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_award_service(): void
    {
        $resolvedAwardService = awarder();

        $this->assertInstanceOf(AwardServiceInterface::class, $resolvedAwardService);
    }
}
