<?php

namespace Tests\Feature\Http\Level;

use App\Models\Level;
use Tests\Traits\HasDummyLevel;
use Illuminate\Support\Facades\{DB, Cache};
use Tests\Feature\Http\BaseIntegrationTesting;

class LevelIndexTest extends BaseIntegrationTesting
{
    use HasDummyLevel;

    /**
     * The levels cache key.
     *
     * @var string
     */
    private const LEVELS_CACHE_KEY = 'gcstatus_levels_key';

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->actingAsDummyUser();
    }

    /**
     * Test if can't get levels if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_levels_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('levels.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get levels route.
     *
     * @return void
     */
    public function test_if_can_get_levels_route(): void
    {
        $this->getJson(route('levels.index'))->assertOk();
    }

    /**
     * Test if can get correct levels json count.
     *
     * @return void
     */
    public function test_if_can_get_correct_levels_json_count(): void
    {
        $this->getJson(route('levels.index'))->assertOk()->assertJsonCount(5, 'data');

        $this->createDummyLevels(2);

        $this->artisan('cache:clear');

        $this->getJson(route('levels.index'))->assertOk()->assertJsonCount(7, 'data');
    }

    /**
     * Test if can get levels from cache on second request.
     *
     * @return void
     */
    public function test_if_can_get_levels_from_cache_on_second_request(): void
    {
        Cache::flush();

        $this->assertFalse(Cache::has(self::LEVELS_CACHE_KEY));

        $this->getJson(route('levels.index'))->assertOk();

        $this->assertTrue(Cache::has(self::LEVELS_CACHE_KEY));

        DB::enableQueryLog();

        $this->getJson(route('levels.index'))->assertOk();

        $forbiddenQuery = 'select * from "levels" where "levels"."deleted_at" is null order by "created_at" desc';

        $queryLog = DB::getQueryLog();

        $this->assertFalse(
            collect($queryLog)->contains(fn (array $query) => $query['query'] === $forbiddenQuery),
            'The forbidden query was executed, but it should have been retrieved from the cache.'
        );
    }

    /**
     * Test if can set the levels on cache on first request.
     *
     * @return void
     */
    public function test_if_can_set_the_levels_on_cache_on_first_request(): void
    {
        Cache::flush();

        DB::enableQueryLog();

        $this->getJson(route('levels.index'))->assertOk();

        $expectedQuery = 'select * from "levels" where "levels"."deleted_at" is null order by "created_at" desc';

        $queryLog = DB::getQueryLog();

        $this->assertTrue(
            collect($queryLog)->contains(fn (array $query) => $query['query'] === $expectedQuery),
            'The expected query was not executed, but it should be on first request.'
        );
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('levels.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'level',
                    'coins',
                    'experience',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->getJson(route('levels.index'))->assertOk()->assertJson([
            'data' => Level::all()->map(function (Level $level) {
                return [
                    'id' => $level->id,
                    'level' => $level->level,
                    'coins' => $level->coins,
                    'experience' => $level->experience,
                ];
            })->toArray(),
        ]);
    }
}
