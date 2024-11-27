<?php

namespace Tests\Feature\Http\ResetPassword;

use App\Models\User;
use Illuminate\Support\Str;
use App\Notifications\PasswordReseted;
use Tests\Feature\Http\BaseIntegrationTesting;
use Illuminate\Support\Facades\{Cache, DB, Hash, Notification};
use App\Contracts\Repositories\ResetPasswordRepositoryInterface;

class ResetTest extends BaseIntegrationTesting
{
    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The reset password repository.
     *
     * @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface
     */
    private ResetPasswordRepositoryInterface $resetPasswordRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->resetPasswordRepository = app(ResetPasswordRepositoryInterface::class);
        $this->user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);
    }

    /**
     * Get valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        $token = $this->resetPasswordRepository->createResetToken($this->user);

        return [
            'token' => $token,
            'email' => $this->user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];
    }

    /**
     * Test if can't reset password without payload.
     *
     * @return void
     */
    public function test_if_cant_reset_password_without_payload(): void
    {
        $this->postJson(route('password.reset'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('password.reset'))
            ->assertUnprocessable()
            ->assertInvalid(['email', 'token', 'password']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('password.reset'))
            ->assertUnprocessable()
            ->assertInvalid(['email', 'token', 'password'])
            ->assertSee('The token field is required. (and 2 more errors)');
    }

    /**
     * Test if can't reset password if email doesn't exist on database.
     *
     * @return void
     */
    public function test_if_cant_reset_password_if_email_doesnt_exist_on_database(): void
    {
        DB::table('password_reset_tokens')->insert([
            'created_at' => now(),
            'token' => $token = Str::random(40),
            'email' => 'valid@gmail.com',
        ]);

        $this->postJson(route('password.reset'), [
            'token' => $token,
            'email' => 'invalid@gmail.com',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['email'])
            ->assertSee('We could not find any user with the given email. Please, double check it and try again!');
    }

    /**
     * Test if can't reset password if token don't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_reset_password_if_token_dont_belongs_to_user(): void
    {
        DB::table('password_reset_tokens')->insert([
            'created_at' => now(),
            'email' => 'another@gmail.com',
            'token' => $token = Str::random(40),
        ]);

        $this->postJson(route('password.reset'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertBadRequest()
            ->assertSee('We could not validate your reset password request. Please, try again later.');
    }

    /**
     * Test if can't reset password if token is already expired.
     *
     * @return void
     */
    public function test_if_cant_reset_password_if_token_is_already_expired(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'created_at' => now()->subHour(),
            'token' => $token = Str::random(40),
        ]);

        $this->postJson(route('password.reset'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertBadRequest()
            ->assertSee('We could not validate your reset password request. Please, try again later.');
    }

    /**
     * Test if can reset password with valid payload.
     *
     * @return void
     */
    public function test_if_can_reset_password_with_valid_payload(): void
    {
        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can change user password.
     *
     * @return void
     */
    public function test_if_can_change_user_password(): void
    {
        $old = $this->user->password;

        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk();

        /** @var \App\Models\User $freshUser */
        $freshUser = $this->user->fresh();

        $current = $freshUser->password;

        $this->assertNotEquals($old, $current);
    }

    /**
     * Test if can hash new user password.
     *
     * @return void
     */
    public function test_if_can_hash_new_user_password(): void
    {
        $old = $this->user->password;

        $this->postJson(route('password.reset'), $data = $this->getValidPayload())->assertOk();

        /** @var string $password */
        $password = $data['password'];

        $this->assertDatabaseMissing('users', [
            'password' => $old,
        ]);

        /** @var \App\Models\User $freshUser */
        $freshUser = $this->user->fresh();

        /** @var string $current */
        $current = $freshUser->password;

        $this->assertTrue(Hash::check($password, $current));
    }

    /**
     * Test if can remove token from database on password reset completion.
     *
     * @return void
     */
    public function test_if_can_remove_token_from_database_on_password_reset_completion(): void
    {
        $data = $this->getValidPayload();

        $this->assertDatabaseCount('password_reset_tokens', 1)->assertDatabaseHas('password_reset_tokens', [
            'email' => $this->user->email,
        ]);

        $this->postJson(route('password.reset'), $data)->assertOk();

        $this->assertDatabaseEmpty('password_reset_tokens')->assertDatabaseMissing('password_reset_tokens', [
            'email' => $this->user->email,
        ]);
    }

    /**
     * Test if can send notification when password reset is successful.
     *
     * @return void
     */
    public function test_if_can_send_notification_when_password_reset_is_successful(): void
    {
        Notification::fake();

        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk();

        Notification::assertSentTo(
            $this->user,
            PasswordReseted::class,
            fn () => true,
        );
    }

    /**
     * Test if can remove cache key for user on reset.
     *
     * @return void
     */
    public function test_if_can_remove_cache_key_for_user_on_reset(): void
    {
        $identifier = $this->user->id;

        $key = "auth.user.$identifier";

        Cache::put($key, $this->user, 60);

        $this->assertTrue(Cache::has($key));

        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'message',
            ],
        ]);
    }

    /**
     * Test if can respond with correct json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_data(): void
    {
        $this->postJson(route('password.reset'), $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'message' => 'Your password was successfully changed!',
            ],
        ]);
    }

    /**
     * Test if can login with new password.
     *
     * @return void
     */
    public function test_if_can_log_in_with_new_password(): void
    {
        $this->postJson(route('password.reset'), $data = $this->getValidPayload())->assertOk();

        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->nickname,
            'password' => $data['password'],
        ])->assertOk();
    }
}
