<?php

namespace Tests\Feature\Http\Admin\Galleriable;

use Tests\Traits\HasDummyGalleriable;
use App\Models\{User, Galleriable, Game, MediaType};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Http\BaseIntegrationTesting;

class GalleriableDestroyTest extends BaseIntegrationTesting
{
    use HasDummyGalleriable;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy galleriable.
     *
     * @var \App\Models\Galleriable
     */
    private Galleriable $galleriable;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'create:galleriables',
        'delete:galleriables',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->bootUserPermissions($this->user);

        $this->galleriable = $this->createDummyGalleriable([
            'galleriable_id' => 1,
            'galleriable_type' => Game::class,
        ]);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('galleriables.destroy', $this->galleriable))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't see if hasn't permissions.
     *
     * @return void
     */
    public function test_if_cant_see_if_hasnt_permissions(): void
    {
        $this->user->permissions()->detach();

        $this->deleteJson(route('galleriables.destroy', $this->galleriable))->assertNotFound();
    }

    /**
     * Test if can delete galleriable from database.
     *
     * @return void
     */
    public function test_if_can_delete_galleriable_from_database(): void
    {
        $this->assertDatabaseHas('galleriables', [
            'id' => $this->galleriable->id,
        ]);

        $this->deleteJson(route('galleriables.destroy', $this->galleriable))->assertOk();

        $this->assertDatabaseMissing('galleriables', [
            'id' => $this->galleriable->id,
        ]);
    }

    /**
     * Test if can remove from storage if file is saved on storage.
     *
     * @return void
     */
    public function test_if_can_remove_from_storage_if_file_is_saved_on_storage(): void
    {
        $id = $this->postJson(route('galleriables.store'), [ // @phpstan-ignore-line
            's3' => true,
            'galleriable_id' => 1,
            'galleriable_type' => Game::class,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
            'file' => $file = UploadedFile::fake()->create('fake.png'),
        ])->json('data')['id'];

        Storage::assertExists('games/' . $file->hashName());

        $this->deleteJson(route('galleriables.destroy', $id))->assertOk();

        Storage::assertMissing('games/' . $file->hashName());
    }
}
