<?php

namespace Tests\Traits;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Create dummy categories.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category>
     */
    public function createDummyCategories(int $times, array $data = []): Collection
    {
        return Category::factory($times)->create($data);
    }
}
