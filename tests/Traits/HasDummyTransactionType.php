<?php

namespace Tests\Traits;

use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTransactionType
{
    /**
     * Create a dummy transaction type.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\TransactionType
     */
    public function createDummyTransactionType(array $data = []): TransactionType
    {
        return TransactionType::factory()->create($data);
    }

    /**
     * Create dummy transaction types.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionType>
     */
    public function createDummyTransactionTypes(int $times, array $data = []): Collection
    {
        return TransactionType::factory($times)->create($data);
    }
}
