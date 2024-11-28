<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Transaction, TransactionType};
use App\Http\Resources\TransactionTypeResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class TransactionTypeResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TransactionTypeResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'type' => 'string',
        'transactions' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<TransactionTypeResource>
     */
    public function resource(): string
    {
        return TransactionTypeResource::class;
    }

    /**
     * Provide a mock instance of TransactionType for testing.
     *
     * @return \App\Models\TransactionType
     */
    public function modelInstance(): TransactionType
    {
        $transactionMock = Mockery::mock(Transaction::class)->makePartial();
        $transactionMock->shouldAllowMockingMethod('getAttribute');
        $transactionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('amount')->andReturn(200);
        $transactionMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->realText());

        $transactionTypeMock = Mockery::mock(TransactionType::class)->makePartial();
        $transactionTypeMock->shouldAllowMockingMethod('getAttribute');
        $transactionTypeMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionTypeMock->shouldReceive('getAttribute')->with('type')->andReturn(TransactionType::ADDITION_TYPE);

        $transactionMock->shouldReceive('getAttribute')->with('transaction_type_id')->andReturn(1);

        $transactionTypeMock->shouldReceive('relationLoaded')
            ->with('transactions')
            ->andReturnTrue();

        $transactionTypeMock->shouldReceive('getAttribute')
            ->with('transactions')
            ->andReturn([$transactionMock]);

        /** @var \App\Models\TransactionType $transactionTypeMock */
        return $transactionTypeMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
