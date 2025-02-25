<?php

namespace Tests\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyLanguage
{
    /**
     * Create a dummy language.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Language
     */
    public function createDummyLanguage(array $data = []): Language
    {
        return Language::factory()->create($data);
    }

    /**
     * Create dummy languages.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Language>
     */
    public function createDummyLanguages(int $times, array $data = []): Collection
    {
        return Language::factory($times)->create($data);
    }
}
