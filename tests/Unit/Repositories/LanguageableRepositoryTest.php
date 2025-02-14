<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Languageable;
use App\Contracts\Repositories\LanguageableRepositoryInterface;

class LanguageableRepositoryTest extends TestCase
{
    /**
     * The Languageable repository.
     *
     * @var \App\Contracts\Repositories\LanguageableRepositoryInterface
     */
    private LanguageableRepositoryInterface $languageableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->languageableRepository = app(LanguageableRepositoryInterface::class);
    }

    /**
     * Test if LanguageableRepository uses the Languageable model correctly.
     *
     * @return void
     */
    public function test_Languageable_repository_uses_Languageable_model(): void
    {
        /** @var \App\Repositories\LanguageableRepository $languageableRepository */
        $languageableRepository = $this->languageableRepository;

        $this->assertInstanceOf(Languageable::class, $languageableRepository->model());
    }
}
