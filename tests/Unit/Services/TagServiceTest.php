<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\TagRepository;
use App\Contracts\Services\TagServiceInterface;
use App\Contracts\Repositories\TagRepositoryInterface;

class TagServiceTest extends TestCase
{
    /**
     * Test if TagService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(TagRepositoryInterface::class, new TagRepository());

        /** @var \App\Services\TagService $tagService */
        $tagService = app(TagServiceInterface::class);

        $this->assertInstanceOf(TagRepository::class, $tagService->repository());
    }
}
