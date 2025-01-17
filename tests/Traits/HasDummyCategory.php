<?php

namespace Tests\Traits;

use App\Models\Category;

trait HasDummyCategory
{
    /**
     * Create a dummy category.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Category
     */
    public function createDummyCategory(array $data = []): Category
    {
        return Category::factory()->create($data);
    }
}
