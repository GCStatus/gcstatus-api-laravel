<?php

namespace Tests\Unit\Requests\Commentable;

use App\Models\Game;
use Database\Seeders\LevelSeeder;
use Tests\Traits\HasDummyCommentable;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Commentable\CommentableStoreRequest;

class CommentableStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyCommentable;

    /**
     * Setup the request test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
    }

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return CommentableStoreRequest::class;
    }

    /**
     * Test if can validate the commentable_id.
     *
     * @return void
     */
    public function test_if_can_validate_the_commentable_id(): void
    {
        $this->assertFalse($this->validate([
            'commentable_id' => '',
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));

        $this->assertFalse($this->validate([
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));

        $this->assertTrue($this->validate([
            'commentable_id' => 123,
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));
    }

    /**
     * Test if can validate the commentable_type.
     *
     * @return void
     */
    public function test_if_can_validate_the_commentable_type(): void
    {
        $this->assertFalse($this->validate([
            'commentable_id' => 123,
            'comment' => fake()->text(),
            'commentable_type' => '',
        ]));

        $this->assertFalse($this->validate([
            'commentable_id' => 123,
            'comment' => fake()->text(),
        ]));

        $this->assertTrue($this->validate([
            'commentable_id' => 123,
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));
    }

    /**
     * Test if can validate the comment.
     *
     * @return void
     */
    public function test_if_can_validate_the_comment(): void
    {
        $this->assertFalse($this->validate([
            'comment' => '',
            'commentable_id' => 123,
            'commentable_type' => fake()->word(),
        ]));

        $this->assertFalse($this->validate([
            'comment' => 123,
            'commentable_id' => 123,
            'commentable_type' => fake()->word(),
        ]));

        $this->assertFalse($this->validate([
            'commentable_id' => 123,
            'commentable_type' => fake()->word(),
        ]));

        $this->assertTrue($this->validate([
            'commentable_id' => 123,
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));
    }

    /**
     * Test if can validate the parent_id.
     *
     * @return void
     */
    public function test_if_can_validate_the_parent_id(): void
    {
        $this->assertFalse($this->validate([
            'parent_id' => 1,
            'commentable_id' => 123,
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
        ]));

        $comment = $this->createDummyCommentable([
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ]);

        $this->assertTrue($this->validate([
            'commentable_id' => 123,
            'parent_id' => $comment->id,
            'comment' => fake()->text(),
            'commentable_type' => fake()->word(),
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
     * @return array<int, array{data: array{comment: mixed, commentable_id: mixed, commentable_type: mixed, parent_id: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'comment' => '',
                    'commentable_id' => '',
                    'commentable_type' => '',
                    'parent_id' => null,
                ],
                'expectedErrors' => [
                    'comment' => 'The comment field is required.',
                    'commentable_id' => 'The commentable id field is required.',
                    'commentable_type' => 'The commentable type field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'comment' => 123,
                    'commentable_id' => null,
                    'commentable_type' => null,
                    'parent_id' => null,
                ],
                'expectedErrors' => [
                    'comment' => 'The comment field must be a string.',
                ],
            ],
            // Case 3: Selected id is doesn't exist on database
            [
                'data' => [
                    'comment' => null,
                    'commentable_id' => null,
                    'commentable_type' => null,
                    'parent_id' => 1,
                ],
                'expectedErrors' => [
                    'parent_id' => 'The selected parent id is invalid.',
                ],
            ],
        ];
    }
}
