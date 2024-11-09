<?php

namespace Tests\Contracts\Models;

interface ShouldTestRelations
{
    /**
     * The contract relations attributes that should be tested.
     *
     * @return void
     */
    public function test_relations_attributes(): void;
}
