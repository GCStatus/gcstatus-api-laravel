<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Models\Game;

class FunctionsTest extends TestCase
{
    /**
     * Test if can normalize a model class name.
     *
     * @return void
     */
    public function test_if_can_normalize_a_model_class_name_to_admin(): void
    {
        $class = Game::class;

        $result = normalizeMorphAdmin($class);

        $this->assertNotEquals($class, $result);

        $this->assertEquals('App\\Models\\GCStatus\\Game', $result);
    }
}
