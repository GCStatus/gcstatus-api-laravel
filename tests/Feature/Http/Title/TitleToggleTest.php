<?php

namespace Tests\Feature\Http\Title;

use App\Models\{User, Title};
use Tests\Traits\HasDummyTitle;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Http\BaseIntegrationTesting;

class TitleToggleTest extends BaseIntegrationTesting
{
    use HasDummyTitle;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy title.
     *
     * @var \App\Models\Title
     */
    private Title $title;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->title = $this->createDummyTitleToUser($this->user);

        DB::table('user_titles')->where('user_id', $this->user->id)->update(['enabled' => false]);
    }

    /**
     * Test if can't toggle the titles if user is not authenticated.
     *
     * @return void
     */
    public function test_if_cant_toggle_the_titles_if_user_is_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('titles.toggle', $this->title))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't enable given title if user hasn't it.
     *
     * @return void
     */
    public function test_if_cant_enable_given_title_if_user_hasnt_it(): void
    {
        DB::table('user_titles')->truncate();

        $this->assertDatabaseEmpty('user_titles');

        $this->putJson(route('titles.toggle', $this->title))->assertOk();

        $this->assertDatabaseEmpty('user_titles');
    }

    /**
     * Test if can enable given title.
     *
     * @return void
     */
    public function test_if_can_enable_given_title(): void
    {
        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);

        $this->putJson(route('titles.toggle', $this->title))->assertOk();

        $this->assertDatabaseHas('user_titles', [
            'enabled' => true,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);
    }

    /**
     * Test if can disable another enabled title if given title is different.
     *
     * @return void
     */
    public function test_if_can_disable_another_enabled_title_if_given_title_is_different(): void
    {
        $title2 = $this->createDummyTitleToUser($this->user);

        DB::table('user_titles')->where('user_id', $this->user->id)->update(['enabled' => false]);

        $this->user->load('titles');

        DB::table('user_titles')
            ->where('title_id', $this->title->id)
            ->where('user_id', $this->user->id)
            ->update(['enabled' => true]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => true,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $title2->id,
        ]);

        $this->putJson(route('titles.toggle', $title2))->assertOk();

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => true,
            'user_id' => $this->user->id,
            'title_id' => $title2->id,
        ]);
    }

    /**
     * Test if can disable all titles for given user if given title is already enabled.
     *
     * @return void
     */
    public function test_if_can_disable_all_titles_for_given_user_if_given_title_is_already_enabled(): void
    {
        $title2 = $this->createDummyTitleToUser($this->user);
        $title3 = $this->createDummyTitleToUser($this->user);

        DB::table('user_titles')->where('user_id', $this->user->id)->update(['enabled' => false]);

        $this->user->load('titles');

        $this->user->titles->each(function (Title $title) {
            $this->assertDatabaseHas('user_titles', [
                'enabled' => false,
                'title_id' => $title->id,
                'user_id' => $this->user->id,
            ]);
        });

        $this->putJson(route('titles.toggle', $this->title))->assertOk();

        $this->assertDatabaseHas('user_titles', [
            'enabled' => true,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $title2->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $title3->id,
        ]);

        $this->putJson(route('titles.toggle', $this->title))->assertOk();

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $title2->id,
        ]);

        $this->assertDatabaseHas('user_titles', [
            'enabled' => false,
            'user_id' => $this->user->id,
            'title_id' => $title3->id,
        ]);
    }

    /**
     * Test if can clear the user cache on title toggle.
     *
     * @return void
     */
    public function test_if_can_clear_the_user_cache_on_title_toggle(): void
    {
        $key = "auth.user.{$this->user->id}";

        $this->getJson(route('auth.me'))->assertOk();

        $this->assertTrue(cacher()->has($key));

        $this->putJson(route('titles.toggle', $this->title))->assertOk();

        $this->assertFalse(cacher()->has($key));
    }
}
