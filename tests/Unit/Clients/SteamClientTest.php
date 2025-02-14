<?php

namespace Tests\Unit\Clients;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Http\Client\Response;
use App\Contracts\Clients\{HttpClientInterface, SteamClientInterface};
use App\Contracts\Services\Validation\SteamResponseValidatorInterface;

class SteamClientTest extends TestCase
{
    /**
     * The steam service.
     *
     * @var \App\Contracts\Clients\SteamClientInterface
     */
    private SteamClientInterface $steamClient;

    /**
     * The http client.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $httpClientMock;

    /**
     * The steam response validator.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $steamValidatorMock;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setUp();

        $this->httpClientMock = Mockery::mock(HttpClientInterface::class);
        $this->steamValidatorMock = Mockery::mock(SteamResponseValidatorInterface::class);

        $this->app->instance(HttpClientInterface::class, $this->httpClientMock);
        $this->app->instance(SteamResponseValidatorInterface::class, $this->steamValidatorMock);

        $this->steamClient = app(SteamClientInterface::class);
    }

    /**
     * Test if can fetch app details and returns a valid response.
     *
     * @return void
     */
    public function test_if_can_fetchAppDetails_and_returns_valid_response()
    {
        /** @var string */
        $baseUrl = config('services.steam.base_url');
        $cc = 'us';
        $appId = '123';

        $response = Mockery::mock(Response::class);

        $response
            ->shouldReceive('json')
            ->once()
            ->andReturn([
                '123' => [
                    'success' => true,
                    'data' => ['name' => 'Half-Life', 'price' => 999],
                ],
            ]);

        $this->httpClientMock
            ->shouldReceive('get')
            ->once()
            ->with($baseUrl, ['l' => 'en', 'cc' => $cc, 'appids' => $appId])
            ->andReturn($response);

        $this->steamValidatorMock->shouldReceive('validate')->once();

        $result = $this->steamClient->fetchAppDetails('123');

        $this->assertEquals(999, $result['price']);
        $this->assertEquals('Half-Life', $result['name']);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
