<?php

namespace Tests\Feature\Http\Home;

use Tests\Feature\Http\BaseIntegrationTesting;

class HomeTest extends BaseIntegrationTesting
{
    /**
     * Test if can get home information if not authenticated.
     *
     * @return void
     */
    public function test_if_can_get_home_information_if_not_authenticated(): void
    {
        $this->getJson(route('home'))->assertOk();
    }

    /**
     * Test if can get correct json response count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_response_count(): void
    {
        $this->getJson(route('home'))->assertOk()->assertJsonCount(6, 'data');
    }
}
