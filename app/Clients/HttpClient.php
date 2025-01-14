<?php

namespace App\Clients;

use Throwable;
use App\Exceptions\GenericException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Contracts\Clients\HttpClientInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $url, array $params = [], array $headers = []): Response
    {
        return $this->handleRequest('get', $url, $params, $headers);
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, array $data = [], array $headers = []): Response
    {
        return $this->handleRequest('post', $url, $data, $headers);
    }

    /**
     * @inheritDoc
     */
    public function put(string $url, array $data = [], array $headers = []): Response
    {
        return $this->handleRequest('put', $url, $data, $headers);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $url, array $data = [], array $headers = []): Response
    {
        return $this->handleRequest('delete', $url, $data, $headers);
    }

    /**
     * Handle HTTP requests with error handling and logging.
     *
     * @param string $method
     * @param string $url
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     * @return \Illuminate\Http\Client\Response
     */
    private function handleRequest(
        string $method,
        string $url,
        array $data = [],
        array $headers = [],
    ): Response {
        try {
            if (empty($data)) {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::withHeaders($headers)->$method($url);
            } else {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::withHeaders($headers)->$method($url, $data);
            }

            if ($response->failed()) {
                logService()->withContext('HTTP Request failed.', [
                    'method' => $method,
                    'url' => $url,
                    'data' => $data,
                    'json' => $response->json(),
                    'code' => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new GenericException('Failed to fetch external data.', $response->status());
            }

            return $response;
        } catch (Throwable $e) {
            logService()->withContext('Exception during HTTP request.', [
                'method' => $method,
                'url' => $url,
                'data' => $data,
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new GenericException('Internal server error.', 500);
        }
    }
}
