<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Language;
use App\Http\Resources\LanguageResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class LanguageResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for LanguageResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'name' => 'string',
        'slug' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<LanguageResource>
     */
    public function resource(): string
    {
        return LanguageResource::class;
    }

    /**
     * Provide a mock instance of Language for testing.
     *
     * @return \App\Models\Language
     */
    public function modelInstance(): Language
    {
        $languageMock = Mockery::mock(Language::class)->makePartial();
        $languageMock->shouldAllowMockingMethod('getAttribute');

        $languageMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $languageMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $languageMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());

        /** @var \App\Models\Language $languageMock */
        return $languageMock;
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