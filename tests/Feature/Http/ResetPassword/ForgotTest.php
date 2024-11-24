<?php

namespace Tests\Feature\Http\ResetPassword;

use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;

class ForgotTest extends BaseIntegrationTesting
{
    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);
    }

    /**
     * Test if can't send reset link without payload.
     *
     * @return void
     */
    public function test_if_cant_send_reset_link_without_payload(): void
    {
        $this->postJson(route('password.notify'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('password.notify'))
            ->assertUnprocessable()
            ->assertInvalid(['email']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('password.notify'))
            ->assertUnprocessable()
            ->assertInvalid(['email'])
            ->assertSee('The email field is required.');
    }

    /**
     * Test if can't send a password reset link if user was not found.
     *
     * @return void
     */
    public function test_if_cant_send_a_password_reset_link_if_user_was_not_found(): void
    {
        $this->postJson(route('password.notify'), ['email' => 'invalid@gmail.com'])
            ->assertUnprocessable()
            ->assertInvalid(['email'])
            ->assertSee('We could not find any user with the given email. Please, double check it and try again!');
    }

    /**
     * Test if can send reset link with valid payload.
     *
     * @return void
     */
    public function test_if_can_send_reset_link_with_valid_payload(): void
    {
        $this->postJson(route('password.notify'), ['email' => $this->user->email])->assertOk();
    }

    /**
     * Test if can create a token on database for reset password.
     *
     * @return void
     */
    public function test_if_can_create_a_token_on_database_for_reset_password(): void
    {
        $this->postJson(route('password.notify'), ['email' => $this->user->email])->assertOk();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $this->user->email,
        ]);
    }

    /**
     * Test if can't send more than one reset link on throttle time out.
     *
     * @return void
     */
    public function test_if_cant_send_more_than_one_reset_link_on_throttle_time_out(): void
    {
        $this->postJson(route('password.notify'), ['email' => $this->user->email])->assertOk();

        $this->postJson(route('password.notify'), ['email' => $this->user->email])
            ->assertBadRequest()
            ->assertSee('You must wait a few seconds to request a password reset again.');
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->postJson(route('password.notify'), ['email' => $this->user->email])->assertOk()->assertJsonStructure([
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
        $this->postJson(route('password.notify'), ['email' => $this->user->email])->assertOk()->assertJson([
            'data' => [
                'message' => 'The password reset link will be sent to your email!',
            ],
        ]);
    }
}
