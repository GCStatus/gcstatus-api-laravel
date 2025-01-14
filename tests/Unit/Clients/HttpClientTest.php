<?php

namespace Tests\Unit\Clients;

use Mockery;
use Exception;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Http\Response;
use App\Exceptions\GenericException;
use Illuminate\Support\Facades\Http;
use App\Contracts\Clients\HttpClientInterface;
use App\Contracts\Services\LogServiceInterface;

class HttpClientTest extends TestCase
{
    /**
     * The log service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $logService;

    /**
     * The http client.
     *
     * @var \App\Contracts\Clients\HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logService = Mockery::mock(LogServiceInterface::class);

        $this->app->instance(LogServiceInterface::class, $this->logService);

        $this->httpClient = app(HttpClientInterface::class);
    }

    /**
     * Test if it handles successfull get request.
     *
     * @return void
     */
    public function test_if_it_handles_successful_get_request(): void
    {
        Http::fake([
            'https://example.com' => Http::response('Success', Response::HTTP_OK),
        ]);

        $response = $this->httpClient->get('https://example.com');

        $this->assertEquals('Success', $response->body());
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    /**
     * Test if it handles successful post request.
     *
     * @return void
     */
    public function test_if_it_handles_successful_post_request(): void
    {
        Http::fake([
            'https://example.com' => Http::response('Created', Response::HTTP_CREATED),
        ]);

        $response = $this->httpClient->post('https://example.com', ['key' => 'value']);

        $this->assertEquals('Created', $response->body());
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    /**
     * Test if it handles successful put request.
     *
     * @return void
     */
    public function test_if_it_handles_successful_put_request(): void
    {
        Http::fake([
            'https://example.com' => Http::response('Updated', Response::HTTP_OK),
        ]);

        $response = $this->httpClient->put('https://example.com', ['key' => 'value']);

        $this->assertEquals('Updated', $response->body());
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    /**
     * Test if it handles successful delete request.
     *
     * @return void
     */
    public function test_if_it_handles_successful_delete_request(): void
    {
        Http::fake([
            'https://example.com' => Http::response('Deleted', Response::HTTP_OK),
        ]);

        $response = $this->httpClient->delete('https://example.com');

        $this->assertEquals('Deleted', $response->body());
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    /**
     * Test if it handles failed http response.
     *
     * @return void
     */
    public function test_if_it_handles_failed_http_response(): void
    {
        $this->logService
            ->shouldReceive('withContext')
            ->twice()
            ->with(Mockery::type('string'), Mockery::type('array'));

        Http::fake([
            'https://example.com' => Http::response('Not Found', 404),
        ]);

        $this->expectException(GenericException::class);
        $this->expectExceptionMessage('Internal server error.');

        $this->httpClient->get('https://example.com');
    }

    /**
     * Test if it handles exception during request.
     *
     * @return void
     */
    public function test_if_it_handles_exception_during_request(): void
    {
        $this->logService
            ->shouldReceive('withContext')
            ->once()
            ->with(
                'Exception during HTTP request.',
                Mockery::subset([
                    'method' => 'get',
                    'url' => 'https://example.com',
                    'data' => [],
                    'code' => 0,
                    'error' => 'Network error',
                ])
            );

        Http::fake(function () {
            throw new Exception('Network error');
        });

        $this->expectException(GenericException::class);
        $this->expectExceptionMessage('Internal server error.');

        $this->httpClient->get('https://example.com');
    }

    /**
     * Test if it logs successful http requests.
     *
     * @return void
     */
    public function test_if_it_logs_failed_http_responses(): void
    {
        $this->logService
            ->shouldReceive('withContext')
            ->twice()
            ->with(
                Mockery::type('string'),
                Mockery::type('array'),
            );

        Http::fake([
            'https://example.com' => Http::response('Not Found', 404),
        ]);

        try {
            $this->httpClient->get('https://example.com');
        } catch (GenericException $e) {
            // Exception is expected, nothing to assert here
        }

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if it logs exceptions during request.
     *
     * @return void
     */
    public function test_if_it_logs_exceptions_during_request(): void
    {
        $this->logService
            ->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::type('string'),
                Mockery::type('array'),
            );

        Http::fake(function () {
            throw new Exception('Network error');
        });

        try {
            $this->httpClient->get('https://example.com');
        } catch (GenericException $e) {
            // Exception is expected, nothing to assert here
        }

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down the test environment.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
