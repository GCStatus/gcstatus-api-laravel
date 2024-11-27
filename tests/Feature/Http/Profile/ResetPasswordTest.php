<?php

namespace Tests\Feature\Http\Profile;

use App\Models\User;
use Illuminate\Support\Facades\{Hash, Cache};
use Tests\Feature\Http\BaseIntegrationTesting;

class ResetPasswordTest extends BaseIntegrationTesting
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
     * Get valid payload.
     *
     * @return array<string, string>
     */
    private function getValidPayload(): array
    {
        return [
            'old_password' => 'admin1234',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];
    }

    /**
     * Test if can't reset password through profile if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_reset_password_through_profile_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('profiles.password.update'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't reset password without payload.
     *
     * @return void
     */
    public function test_if_cant_reset_password_without_payload(): void
    {
        $this->putJson(route('profiles.password.update'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('profiles.password.update'))
            ->assertUnprocessable()
            ->assertInvalid(['old_password', 'password']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('profiles.password.update'))
            ->assertUnprocessable()
            ->assertInvalid(['old_password', 'password'])
            ->assertSee('The old password field is required. (and 1 more error)');
    }

    /**
     * Test if can't update password if old password don't match.
     *
     * @return void
     */
    public function test_if_cant_update_password_if_old_password_dont_match(): void
    {
        $this->putJson(route('profiles.password.update'), [
            'old_password' => 'invalid_pass',
            'password' => 'Admin1234!@#$!',
            'password_confirmation' => 'Admin1234!@#$!',
        ])->assertBadRequest()->assertSee('Your password does not match.');
    }

    /**
     * Test if can't reset password if new password is invalid.
     *
     * @return void
     */
    public function test_if_cant_reset_password_if_new_password_is_invalid(): void
    {
        // Case 1: Password is unconfirmed
        $unconfirmed = [
            'old_password' => 'admin1234',
            'password' => ']3N"g&D8pF7?',
        ];

        $this->putJson(route('profiles.password.update'), $unconfirmed)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 2: Unmatch password confirmation
        $unmatched = [
            'old_password' => 'admin1234',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
        ];

        $this->putJson(route('profiles.password.update'), $unmatched)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 3: Password has no uppercases
        $nonUppercases = [
            'old_password' => 'admin1234',
            'password' => ']3n"g&d8pf7?',
            'password_confirmation' => ']3n"g&d8pf7?',
        ];

        $this->putJson(route('profiles.password.update'), $nonUppercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 4: Password has no lowercases
        $nonLowercases = [
            'old_password' => 'admin1234',
            'password' => ']3N"G&D8PF7?',
            'password_confirmation' => ']3N"G&D8PF7?',
        ];

        $this->putJson(route('profiles.password.update'), $nonLowercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 5: Password has no letters
        $nonLetters = [
            'old_password' => 'admin1234',
            'password' => ']32"!&98#@7?',
            'password_confirmation' => ']32"!&98#@7?',
        ];

        $this->putJson(route('profiles.password.update'), $nonLetters)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 6: Password has no number
        $nonNumbers = [
            'old_password' => 'admin1234',
            'password' => ']Aa"!&VD#@%?',
            'password_confirmation' => ']Aa"!&VD#@%?',
        ];

        $this->putJson(route('profiles.password.update'), $nonNumbers)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one number.');

        // Case 7: Password has no symbols
        $nonSymbols = [
            'old_password' => 'admin1234',
            'password' => 'NoSymbol123CD',
            'password_confirmation' => 'NoSymbol123CD',
        ];

        $this->putJson(route('profiles.password.update'), $nonSymbols)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one symbol.');

        // Case 8: Password has not at least 8 chars
        $non8chars = [
            'old_password' => 'admin1234',
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
        ];

        $this->putJson(route('profiles.password.update'), $non8chars)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must be at least 8 characters.');

        // Case 9: Password is compromised
        $compromised = [
            'old_password' => 'admin1234',
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
        ];

        $this->putJson(route('profiles.password.update'), $compromised)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The given password has appeared in a data leak. Please choose a different password.');
    }

    /**
     * Test if can reset the current password with valid payload.
     *
     * @return void
     */
    public function test_if_can_reset_the_current_password_with_valid_payload(): void
    {
        $this->putJson(route('profiles.password.update'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can update the user password hash.
     *
     * @return void
     */
    public function test_if_can_update_the_user_password_hash(): void
    {
        $old = $this->user->password;

        $this->putJson(route('profiles.password.update'), $data = $this->getValidPayload())->assertOk();

        /** @var \App\Models\User $user */
        $user = $this->user->fresh();

        /** @var string $new */
        $new = $user->password;

        $this->assertNotEquals($old, $new);

        $this->assertTrue(Hash::check($data['password'], $new));
    }

    /**
     * Test if can remove user cache on password change.
     *
     * @return void
     */
    public function test_if_can_remove_user_cache_on_password_change(): void
    {
        $this->getJson(route('auth.me'))->assertOk();

        $identifier = $this->user->id;

        $key = "auth.user.$identifier";

        $this->assertTrue(Cache::has($key));

        $this->putJson(route('profiles.password.update'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can login with new password.
     *
     * @return void
     */
    public function test_if_can_log_in_with_new_password(): void
    {
        $this->putJson(route('profiles.password.update'), $data = $this->getValidPayload())->assertOk();

        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->nickname,
            'password' => $data['password'],
        ])->assertOk();
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->putJson(route('profiles.password.update'), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('profiles.password.update'), $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'message' => 'Your password was successfully updated!',
            ],
        ]);
    }
}
