<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{Language, Languageable};
use App\Http\Resources\Admin\LanguageableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class LanguageableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for LanguageableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'menu' => 'bool',
        'dubs' => 'bool',
        'subtitles' => 'bool',
        'created_at' => 'string',
        'updated_at' => 'string',
        'language' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<LanguageableResource>
     */
    public function resource(): string
    {
        return LanguageableResource::class;
    }

    /**
     * Provide a mock instance of Languageable for testing.
     *
     * @return \App\Models\Languageable
     */
    public function modelInstance(): Languageable
    {
        $languageMock = Mockery::mock(Language::class)->makePartial();

        $languageableMock = Mockery::mock(Languageable::class)->makePartial();
        $languageableMock->shouldAllowMockingMethod('getAttribute');

        $languageableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $languageableMock->shouldReceive('getAttribute')->with('menu')->andReturn(fake()->boolean());
        $languageableMock->shouldReceive('getAttribute')->with('dubs')->andReturn(fake()->boolean());
        $languageableMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $languageableMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());
        $languageableMock->shouldReceive('getAttribute')->with('subtitles')->andReturn(fake()->boolean());

        $languageableMock->shouldReceive('getAttribute')->with('language')->andReturn($languageMock);

        /** @var \App\Models\Languageable $languageableMock */
        return $languageableMock;
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
