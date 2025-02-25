<?php

namespace Tests\Unit\Requests\Admin\TransactionType;

use Tests\Traits\HasDummyTransactionType;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\TransactionType\TransactionTypeStoreRequest;

class TransactionTypeStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyTransactionType;

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return TransactionTypeStoreRequest::class;
    }

    /**
     * Test if can validate the type.
     *
     * @return void
     */
    public function test_if_can_validate_the_type(): void
    {
        $this->assertFalse($this->validate([
            'type' => '',
        ]));

        $this->assertFalse($this->validate([
            'type' => 123,
        ]));

        $transactionType = $this->createDummyTransactionType();

        $this->assertFalse($this->validate([
            'type' => $transactionType->type,
        ]));

        $this->assertTrue($this->validate([
            'type' => fake()->word(),
        ]));
    }

    /**
     * Test invalid validation cases.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $expectedErrors
     * @return void
     */
    #[DataProvider('invalidDataProvider')]
    public function test_invalid_validation_fails(array $data, array $expectedErrors): void
    {
        if (isset($data['type']) && $data['type'] === $type = 'Duplicated') {
            $this->createDummyTransactionType([
                'type' => $type,
            ]);
        }

        $this->assertFalse($this->validate($data));

        $errors = $this->getValidationErrors($data);

        foreach ($expectedErrors as $field => $expectedMessage) {
            $this->assertArrayHasKey($field, $errors);
            $this->assertStringContainsString($expectedMessage, $errors[$field][0]);
        }
    }

    /**
     * Data provider for invalid validation cases.
     *
     * @return array<int, mixed>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'type' => '',
                ],
                'expectedErrors' => [
                    'type' => 'The type field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'type' => 123,
                ],
                'expectedErrors' => [
                    'type' => 'The type field must be a string.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'type' => 'Duplicated',
                ],
                'expectedErrors' => [
                    'type' => 'The type has already been taken.',
                ],
            ],
        ];
    }
}
