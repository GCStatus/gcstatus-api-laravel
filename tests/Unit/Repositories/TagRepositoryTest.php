<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Tag;
use App\Contracts\Repositories\TagRepositoryInterface;

class TagRepositoryTest extends TestCase
{
    /**
     * The Tag repository.
     *
     * @var \App\Contracts\Repositories\TagRepositoryInterface
     */
    private TagRepositoryInterface $tagRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = app(TagRepositoryInterface::class);
    }

    /**
     * Test if TagRepository uses the Tag model correctly.
     *
     * @return void
     */
    public function test_Tag_repository_uses_Tag_model(): void
    {
        /** @var \App\Repositories\TagRepository $tagRepository */
        $tagRepository = $this->tagRepository;

        $this->assertInstanceOf(Tag::class, $tagRepository->model());
    }
}
