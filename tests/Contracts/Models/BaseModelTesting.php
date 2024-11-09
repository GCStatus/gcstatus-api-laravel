<?php

namespace Tests\Contracts\Models;

use Tests\TestCase;
use Tests\Traits\UnitModelHelpers;

abstract class BaseModelTesting extends TestCase implements ShouldTestModels
{
    use UnitModelHelpers;

    /**
     * Setup the tests.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }
}
