<?php

namespace Tests\Feature\Http\Profile;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Http\BaseIntegrationTesting;
use Illuminate\Support\Facades\{Cache, Storage};

class PictureUpdateTest extends BaseIntegrationTesting
{
    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
    }

    /**
     * Get the valid payload.
     *
     * @return array<string, \Illuminate\Http\UploadedFile>
     */
    public function getValidPayload(): array
    {
        return [
            'file' => UploadedFile::fake()->create('fake.png'),
        ];
    }

    /**
     * Test if can't update the profile picture if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_update_the_profile_picture_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('profiles.picture.update'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't update profile picture without payload.
     *
     * @return void
     */
    public function test_if_cant_update_profile_picture_without_payload(): void
    {
        $this->putJson(route('profiles.picture.update'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('profiles.picture.update'))
            ->assertUnprocessable()
            ->assertInvalid(['file']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('profiles.picture.update'))
            ->assertUnprocessable()
            ->assertInvalid(['file'])
            ->assertSee('The file field is required.');
    }

    /**
     * Test if can't update a picture with invalid mime type.
     *
     * @return void
     */
    public function test_if_cant_update_a_picture_with_invalid_mime_type(): void
    {
        $this->putJson(route('profiles.picture.update'), [
            'file' => UploadedFile::fake()->create('invalid.pdf'),
        ])->assertUnprocessable()
            ->assertInvalid(['file'])
            ->assertSee('The file field must be a file of type: jpg, bmp, png, jpeg, gif.');
    }

    /**
     * Test if can't update a picture with invalid image size.
     *
     * @return void
     */
    public function test_if_cant_update_a_picture_with_invalid_image_size(): void
    {
        $this->putJson(route('profiles.picture.update'), [
            'file' => UploadedFile::fake()->create('valid.png', 3000),
        ])->assertUnprocessable()
            ->assertInvalid(['file'])
            ->assertSee('The file field must not be greater than 2048 kilobytes.');
    }

    /**
     * Test if can update a picture with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_a_picture_with_valid_payload(): void
    {
        $this->putJson(route('profiles.picture.update'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the picture on storage and database.
     *
     * @return void
     */
    public function test_if_can_save_the_picture_on_storage_and_database(): void
    {
        Storage::assertDirectoryEmpty('profiles');

        $nickname = $this->user->nickname;

        $this->putJson(route('profiles.picture.update'), $data = $this->getValidPayload())->assertOk();

        $extension = $data['file']->getClientOriginalExtension();

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->id,
            'photo' => $key = "profiles/{$nickname}_profile_picture.$extension",
        ]);

        Storage::assertExists($key);
    }

    /**
     * Test if can replace the current user picture and remove old from storage.
     *
     * @return void
     */
    public function test_if_can_replace_the_current_user_picture_and_remove_old_from_storage(): void
    {
        $nickname = $this->user->nickname;

        $this->putJson(route('profiles.picture.update'), $data = $this->getValidPayload())->assertOk();

        $extension = $data['file']->getClientOriginalExtension();

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->id,
            'photo' => $old = "profiles/{$nickname}_profile_picture.$extension",
        ]);

        Storage::assertExists($old);

        $this->user->update([
            'nickname' => 'another',
        ]);

        $this->user->fresh();

        $this->putJson(route('profiles.picture.update'), $this->getValidPayload())->assertOk();

        $this->assertDatabaseMissing('profiles', [
            'user_id' => $this->user->id,
            'photo' => $old,
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->id,
            'photo' => $new = "profiles/another_profile_picture.$extension",
        ]);

        Storage::assertExists($new);

        Storage::assertMissing($old);
    }

    /**
     * Test if can remove user cache on profile update.
     *
     * @return void
     */
    public function test_if_can_remove_user_cache_on_profile_update(): void
    {
        $identifier = $this->user->id;

        $key = "auth.user.$identifier";

        $this->getJson(route('auth.me'))->assertOk();

        $this->assertTrue(Cache::has($key));

        $this->putJson(route('profiles.picture.update'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can respond with valid json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_structure(): void
    {
        $this->putJson(route('profiles.picture.update'), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'level',
                'nickname',
                'birthdate',
                'experience',
                'created_at',
                'updated_at',
                'profile' => [
                    'id',
                    'photo',
                    'share',
                    'phone',
                    'twitch',
                    'github',
                    'twitter',
                    'youtube',
                    'facebook',
                    'instagram',
                ],
            ],
        ]);
    }

    /**
     * Test if can respond with valid json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_data(): void
    {
        $data = $this->getValidPayload();
        $nickname = $this->user->nickname;
        $extension = $data['file']->getClientOriginalExtension();

        $path = "profiles/{$nickname}_profile_picture.$extension";

        $this->putJson(route('profiles.picture.update'), $data)->assertOk()->assertJson([
            'data' => [
                'profile' => [
                    'id' => $this->user->id,
                    'photo' => storage()->getPath($path),
                ],
            ],
        ]);
    }
}
