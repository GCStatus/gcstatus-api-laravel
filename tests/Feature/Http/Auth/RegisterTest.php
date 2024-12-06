<?php

namespace Tests\Feature\Http\Auth;

use Tests\Feature\Http\BaseIntegrationTesting;
use Illuminate\Support\Facades\{DB, Hash, Cookie};

class RegisterTest extends BaseIntegrationTesting
{
    /**
     * Get valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'birthdate' => today()->subYears(14)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];
    }
    /**
     * Test if can't register without payload.
     *
     * @return void
     */
    public function test_if_cant_register_without_payload(): void
    {
        $this->postJson(route('auth.register'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct register invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_register_invalid_json_keys(): void
    {
        $this->postJson(route('auth.register'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'email', 'nickname', 'birthdate', 'password']);
    }

    /**
     * Test if can throw correct register invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_register_invalid_json_messages(): void
    {
        $this->postJson(route('auth.register'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'email', 'nickname', 'birthdate', 'password'])
            ->assertSee('The name field is required. (and 4 more errors)');
    }

    /**
     * Test if can't register with duplicated nickname.
     *
     * @return void
     */
    public function test_if_cant_register_with_duplicated_nickname(): void
    {
        $user = $this->createDummyUser();

        $this->postJson(route('auth.register'), [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => $user->nickname,
            'birthdate' => today()->subYears(14)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['nickname'])
            ->assertSee('The providen nickname is already in use.');
    }

    /**
     * Test if can't register with duplicated email.
     *
     * @return void
     */
    public function test_if_cant_register_with_duplicated_email(): void
    {
        $user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);

        $this->postJson(route('auth.register'), [
            'name' => fake()->name(),
            'email' => $user->email,
            'nickname' => fake()->userName(),
            'birthdate' => today()->subYears(14)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['email'])
            ->assertSee('The providen email is already in use.');
    }

    /**
     * Test if can't register if user has less than 14 years old.
     *
     * @return void
     */
    public function test_if_cant_register_if_user_has_less_than_14_years_old(): void
    {
        $this->postJson(route('auth.register'), [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'birthdate' => today()->subYears(13)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['birthdate'])
            ->assertSee('You should have at least 14 years old to proceed.');
    }

    /**
     * Test if can't register if password is invalid.
     *
     * @return void
     */
    public function test_if_cant_register_if_password_is_invalid(): void
    {
        // Case 1: Password is unconfirmed
        $unconfirmed = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $unconfirmed)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 2: Unmatch password confirmation
        $unmatched = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $unmatched)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 3: Password has no uppercases
        $nonUppercases = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3n"g&d8pf7?',
            'password_confirmation' => ']3n"g&d8pf7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $nonUppercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 4: Password has no lowercases
        $nonLowercases = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"G&D8PF7?',
            'password_confirmation' => ']3N"G&D8PF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $nonLowercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 5: Password has no letters
        $nonLetters = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']32"!&98#@7?',
            'password_confirmation' => ']32"!&98#@7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $nonLetters)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 6: Password has no number
        $nonNumbers = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']Aa"!&VD#@%?',
            'password_confirmation' => ']Aa"!&VD#@%?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $nonNumbers)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one number.');

        // Case 7: Password has no symbols
        $nonSymbols = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => 'NoSymbol123CD',
            'password_confirmation' => 'NoSymbol123CD',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $nonSymbols)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one symbol.');

        // Case 8: Password has not at least 8 chars
        $non8chars = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $non8chars)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must be at least 8 characters.');

        // Case 9: Password is compromised
        $compromised = [
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->postJson(route('auth.register'), $compromised)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The given password has appeared in a data leak. Please choose a different password.');
    }

    /**
     * Test if can register if payload is correct.
     *
     * @return void
     */
    public function test_if_can_register_if_payload_is_correct(): void
    {
        $this->postJson(route('auth.register'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the registered user payload on database.
     *
     * @return void
     */
    public function test_if_can_save_the_registered_user_payload_on_database(): void
    {
        $this->postJson(route('auth.register'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'nickname' => $data['nickname'],
            'birthdate' => $data['birthdate'],
        ]);
    }

    /**
     * Test if can save the password hash correctly on register.
     *
     * @return void
     */
    public function test_if_can_save_the_password_hash_correctly_on_register(): void
    {
        $this->postJson(route('auth.register'), $data = $this->getValidPayload())->assertCreated();

        /** @var string $current */
        $current = $data['password'];

        /** @var string $toCheck */
        $toCheck = DB::table('users')->value('password');

        $this->assertTrue(Hash::check($current, $toCheck));
    }

    /**
     * Test if can associate an empty wallet to registered user.
     *
     * @return void
     */
    public function test_if_can_associate_an_empty_wallet_to_registered_user(): void
    {
        $this->postJson(route('auth.register'), $this->getValidPayload())->assertCreated();

        /** @var int $userId */
        $userId = DB::table('users')->value('id');

        $this->assertDatabaseHas('wallets', [
            'balance' => 0,
            'user_id' => $userId,
        ]);
    }

    /**
     * Test if can associate an empty profile to registered user.
     *
     * @return void
     */
    public function test_if_can_associate_an_empty_profile_to_registered_user(): void
    {
        $this->postJson(route('auth.register'), $this->getValidPayload())->assertCreated();

        /** @var int $userId */
        $userId = DB::table('users')->value('id');

        $this->assertDatabaseHas('profiles', [
            'share' => false,
            'user_id' => $userId,
        ]);
    }

    /**
     * Test if can set authentication cookies on response after register.
     *
     * @return void
     */
    public function test_if_can_set_authentication_cookies_on_response_after_register(): void
    {
        $this->postJson(route('auth.register'), $this->getValidPayload())->assertCreated();

        /** @var string $tokenKey*/
        $tokenKey = config('auth.token_key');
        Cookie::hasQueued($tokenKey);

        /** @var string $isAuthKey */
        $isAuthKey = config('auth.is_auth_key');
        Cookie::hasQueued($isAuthKey);
    }

    /**
     * Test if can get correct json structure on register response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_on_register_response(): void
    {
        $this->postJson(route('auth.register'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
            ],
        ]);
    }

    /**
     * Test if can get correct json data on register response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data_on_register_response(): void
    {
        $this->postJson(route('auth.register'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'nickname' => $data['nickname'],
                'birthdate' => $data['birthdate'],
            ],
        ]);
    }
}
