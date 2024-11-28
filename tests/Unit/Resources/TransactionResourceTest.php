<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\TransactionResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Models\{User, Transaction, TransactionType};

class TransactionResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TransactionResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'amount' => 'int',
        'description' => 'string',
        'created_at' => 'string',
        'user' => 'object',
        'type' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<TransactionResource>
     */
    public function resource(): string
    {
        return TransactionResource::class;
    }

    /**
     * Provide a mock instance of Transaction for testing.
     *
     * @return \App\Models\Transaction
     */
    public function modelInstance(): Transaction
    {
        $transactionTypeMock = Mockery::mock(TransactionType::class);
        $transactionTypeMock->shouldReceive('getAttribute')->with('type')->andReturn(TransactionType::ADDITION_TYPE);

        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $transactionMock = Mockery::mock(Transaction::class)->makePartial();
        $transactionMock->shouldAllowMockingMethod('getAttribute');

        $transactionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('amount')->andReturn(fake()->numberBetween(10, 1000));
        $transactionMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->realText());
        $transactionMock->shouldReceive('getAttribute')->with('created_at')->andReturn(now()->toISOString());
        $transactionMock->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('transaction_type_id')->andReturn(TransactionType::ADDITION_TYPE_ID);

        $transactionMock->shouldReceive('getAttribute')->with('type')->andReturn($transactionTypeMock);
        $transactionMock->shouldReceive('getAttribute')->with('user')->andReturn($userMock);

        /** @var \App\Models\Transaction $castedTransaction */
        $castedTransaction = $transactionMock;

        return $castedTransaction;
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
