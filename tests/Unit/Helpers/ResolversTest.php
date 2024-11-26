<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Contracts\Services\StorageServiceInterface;

class ResolversTest extends TestCase
{
    /**
     * Test if can get correct binded interface for storage service.
     *
     * @return void
     */
    public function test_if_can_get_correct_binded_interface_for_storage_service(): void
    {
        $resolvedStorage = storage();

        $this->assertInstanceOf(StorageServiceInterface::class, $resolvedStorage);
    }
}
