<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\MediaTypeRepository;
use App\Contracts\Services\MediaTypeServiceInterface;
use App\Contracts\Repositories\MediaTypeRepositoryInterface;

class MediaTypeServiceTest extends TestCase
{
    /**
     * Test if MediaTypeService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(MediaTypeRepositoryInterface::class, new MediaTypeRepository());

        /** @var \App\Services\MediaTypeService $mediaTypeService */
        $mediaTypeService = app(MediaTypeServiceInterface::class);

        $this->assertInstanceOf(MediaTypeRepository::class, $mediaTypeService->repository());
    }
}
