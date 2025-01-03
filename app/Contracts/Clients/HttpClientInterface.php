<?php

namespace App\Contracts\Clients;

use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    /**
     * Handle GET requests.
     *
     * @param string $url
     * @param array<string, mixed> $params
     * @param array<string, mixed> $headers
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $url, array $params = [], array $headers = []): Response;

    /**
     * Handle POST requests.
     *
     * @param string $url
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     * @return \Illuminate\Http\Client\Response
     */
    public function post(string $url, array $data = [], array $headers = []): Response;

    /**
     * Handle PUT requests.
     *
     * @param string $url
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     * @return \Illuminate\Http\Client\Response
     */
    public function put(string $url, array $data = [], array $headers = []): Response;

    /**
     * Handle DELETE requests.
     *
     * @param string $url
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     * @return \Illuminate\Http\Client\Response
     */
    public function delete(string $url, array $data = [], array $headers = []): Response;
}
