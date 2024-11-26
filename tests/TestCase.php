<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var string $fakeFilesystemDisk */
        $fakeFilesystemDisk = env('FILESYSTEM_DISK', 'aws');

        Storage::fake($fakeFilesystemDisk);
    }
}
