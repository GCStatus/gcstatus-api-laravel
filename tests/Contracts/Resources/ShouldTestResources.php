<?php

namespace Tests\Contracts\Resources;

use Illuminate\Database\Eloquent\Model;

interface ShouldTestResources
{
    /**
     * The contract to get resource class-string.
     *
     * @return class-string
     */
    public function resource(): string;

    /**
     * The testable model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function modelInstance(): Model;
}
