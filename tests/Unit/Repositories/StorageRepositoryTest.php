<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use League\Flysystem\Visibility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Repositories\StorageRepositoryInterface;

class StorageRepositoryTest extends TestCase
{
    /**
     * The storage repository.
     *
     * @var \App\Contracts\Repositories\StorageRepositoryInterface
     */
    private StorageRepositoryInterface $storageRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storageRepository = app(StorageRepositoryInterface::class);
    }

    /**
     * Test if can create a new file on storage.
     *
     * @return void
     */
    public function test_if_can_create_a_new_file_on_storage(): void
    {
        $folder = 'fakes';
        $visibility = Visibility::PRIVATE;
        $file = UploadedFile::fake()->create('fake.png');

        Storage::shouldReceive('put')
            ->once()
            ->with($folder, $file, $visibility)
            ->andReturn('fakes/path_to_storage_file.png');

        $result = $this->storageRepository->create($file, $folder, $visibility);

        $this->assertEquals($result, "fakes/path_to_storage_file.png");
    }

    /**
     * Test if can check if a file exists on storage.
     *
     * @return void
     */
    public function test_if_can_check_if_a_file_exists_on_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        Storage::shouldReceive('exists')
            ->once()
            ->with($path)
            ->andReturnTrue();

        $result = $this->storageRepository->exists($path);

        $this->assertTrue($result);
    }

    /**
     * Test if can create a new file with alias on storage.
     *
     * @return void
     */
    public function test_if_can_create_a_new_file_with_alias_on_storage(): void
    {
        $folder = 'fakes';
        $alias = 'my-file';
        $visibility = Visibility::PRIVATE;
        $file = UploadedFile::fake()->create('fake.png');

        Storage::shouldReceive('putFileAs')
            ->once()
            ->with($folder, $file, $alias, $visibility)
            ->andReturn("fakes/$alias.png");

        $result = $this->storageRepository->createAs($file, $folder, $alias, $visibility);

        $this->assertEquals($result, "fakes/$alias.png");
    }

    /**
     * Test if can delete a file from storage.
     *
     * @return void
     */
    public function test_if_can_delete_a_file_from_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        Storage::shouldReceive('delete')
            ->once()
            ->with($path);

        $this->storageRepository->delete($path);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get the correct path from storage.
     *
     * @return void
     */
    public function test_if_can_get_the_correct_path_from_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        Storage::shouldReceive('temporaryUrl')
            ->once()
            ->with($path, Mockery::any())
            ->andReturn($expected = "https://path.to.aws/$path");

        $result = $this->storageRepository->getPath($path);

        $this->assertEquals($result, $expected);
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
