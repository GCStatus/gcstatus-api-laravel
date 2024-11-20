<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Http\BaseIntegrationTesting;

class CompleteRegistrationTest extends BaseIntegrationTesting
{
    /**
     * The dummy authenticated user.
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
     * The valid dummy payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'nickname' => fake()->userName(),
            'birthdate' => today()->subYears(14)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];
    }

    /**
     * Test if can't complete registration if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_complete_registration_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('auth.register.complete'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't complete registration without payload.
     *
     * @return void
     */
    public function test_if_cant_complete_registration_without_payload(): void
    {
        $this->putJson(route('auth.register.complete'))->assertUnprocessable();
    }

    /**
     * Test if can get correct invalid payload json keys.
     *
     * @return void
     */
    public function test_if_can_get_correct_invalid_payload_json_keys(): void
    {
        $this->putJson(route('auth.register.complete'))
            ->assertUnprocessable()
            ->assertInvalid(['nickname', 'birthdate', 'password']);
    }

    /**
     * Test if can get correct invalid payload json messages.
     *
     * @return void
     */
    public function test_if_can_get_correct_invalid_payload_json_messages(): void
    {
        $this->putJson(route('auth.register.complete'))
            ->assertUnprocessable()
            ->assertInvalid(['nickname', 'birthdate', 'password'])
            ->assertSee('The nickname field is required. (and 2 more errors)');
    }

    /**
     * Test if can't choose an already used nickname.
     *
     * @return void
     */
    public function test_if_cant_choose_an_already_used_nickname(): void
    {
        $anotherUser = $this->createDummyUser();

        $this->putJson(route('auth.register.complete'), [
            'nickname' => $anotherUser->nickname,
            'birthdate' => today()->subYears(15)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['nickname'])
            ->assertSee('The providen nickname is already in use.');
    }

    /**
     * Test if can't complete registration if user has less than 14 years old.
     *
     * @return void
     */
    public function test_if_cant_complete_registration_if_user_has_less_than_14_years_old(): void
    {
        $this->putJson(route('auth.register.complete'), [
            'nickname' => fake()->userName(),
            'birthdate' => today()->subYears(13)->toDateString(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ])->assertUnprocessable()
            ->assertInvalid(['birthdate'])
            ->assertSee('You should have at least 14 years old to proceed.');
    }

    /**
     * Test if can't complete registration if password is invalid.
     *
     * @return void
     */
    public function test_if_cant_complete_registration_if_password_is_invalid(): void
    {
        // Case 1: Password is unconfirmed
        $unconfirmed = [
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $unconfirmed)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 2: Unmatch password confirmation
        $unmatched = [
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $unmatched)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field confirmation does not match.');

        // Case 3: Password has no uppercases
        $nonUppercases = [
            'nickname' => fake()->userName(),
            'password' => ']3n"g&d8pf7?',
            'password_confirmation' => ']3n"g&d8pf7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $nonUppercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 4: Password has no lowercases
        $nonLowercases = [
            'nickname' => fake()->userName(),
            'password' => ']3N"G&D8PF7?',
            'password_confirmation' => ']3N"G&D8PF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $nonLowercases)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 5: Password has no letters
        $nonLetters = [
            'nickname' => fake()->userName(),
            'password' => ']32"!&98#@7?',
            'password_confirmation' => ']32"!&98#@7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $nonLetters)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one uppercase and one lowercase letter.');

        // Case 6: Password has no number
        $nonNumbers = [
            'nickname' => fake()->userName(),
            'password' => ']Aa"!&VD#@%?',
            'password_confirmation' => ']Aa"!&VD#@%?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $nonNumbers)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one number.');

        // Case 7: Password has no symbols
        $nonSymbols = [
            'nickname' => fake()->userName(),
            'password' => 'NoSymbol123CD',
            'password_confirmation' => 'NoSymbol123CD',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $nonSymbols)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must contain at least one symbol.');

        // Case 8: Password has not at least 8 chars
        $non8chars = [
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $non8chars)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The password field must be at least 8 characters.');

        // Case 9: Password is compromised
        $compromised = [
            'nickname' => fake()->userName(),
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ];

        $this->putJson(route('auth.register.complete'), $compromised)
            ->assertUnprocessable()
            ->assertInvalid(['password'])
            ->assertSee('The given password has appeared in a data leak. Please choose a different password.');
    }

    /**
     * Test if can complete registration if payload is correct.
     *
     * @return void
     */
    public function test_if_can_complete_registration_if_payload_is_correct(): void
    {
        $this->putJson(route('auth.register.complete'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can choose an already used nickname if nickname is mine.
     *
     * @return void
     */
    public function test_if_can_choose_an_already_used_nickname_if_nickname_is_mine(): void
    {
        $data = $this->getValidPayload();
        $data = [
            'nickname' => $this->user->nickname,
            'birthdate' => $data['birthdate'],
            'password' => $data['password'],
            'password_confirmation' => $data['password_confirmation'],
        ];

        $this->putJson(route('auth.register.complete'), $data)->assertOk();
    }

    /**
     * Test if can save the complete registration payload on database.
     *
     * @return void
     */
    public function test_if_can_save_the_complete_registration_payload_on_database(): void
    {
        $this->putJson(route('auth.register.complete'), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('users', [
            'nickname' => $data['nickname'],
            'birthdate' => $data['birthdate'],
        ]);
    }

    /**
     * Test if can save the password hash correctly on complete registration.
     *
     * @return void
     */
    public function test_if_can_save_the_password_hash_correctly_on_complete_registration(): void
    {
        $this->putJson(route('auth.register.complete'), $data = $this->getValidPayload())->assertOk();

        /** @var \App\Models\User $user */
        $user = $this->user->fresh();

        /** @var string $current */
        $current = $data['password'];

        /** @var string $toCheck */
        $toCheck = $user->password;

        $this->assertTrue(Hash::check($current, $toCheck));
    }

    /**
     * Test if can get correct json structure on complete regsitration response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_on_complete_registration_response(): void
    {
        $this->putJson(route('auth.register.complete'), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
     * Test if can get correct json data on complete registration response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data_on_complete_registration_response(): void
    {
        /** @var \App\Models\Level $level */
        $level = $this->user->level;

        $this->putJson(route('auth.register.complete'), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'level' => $level->level,
                'nickname' => $data['nickname'],
                'birthdate' => $data['birthdate'],
                'experience' => $this->user->experience,
            ],
        ]);
    }
}
