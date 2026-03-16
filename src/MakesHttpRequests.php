<?php

declare(strict_types=1);

namespace Laravel\Forge;

use Exception;
use Laravel\Forge\Exceptions\FailedActionException;
use Laravel\Forge\Exceptions\ForbiddenException;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Exceptions\RateLimitExceededException;
use Laravel\Forge\Exceptions\TimeoutException;
use Laravel\Forge\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

trait MakesHttpRequests
{
    /**
     * Make a GET request to Forge servers and return the response.
     */
    public function get(string $uri): mixed
    {
        return $this->request('GET', $uri);
    }

    /**
     * Make a POST request to Forge servers and return the response.
     */
    public function post(string $uri, array $payload = []): mixed
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Forge servers and return the response.
     */
    public function put(string $uri, array $payload = []): mixed
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a PATCH request to Forge servers and return the response.
     */
    public function patch(string $uri, array $payload = []): mixed
    {
        return $this->request('PATCH', $uri, $payload);
    }

    /**
     * Make a DELETE request to Forge servers and return the response.
     */
    public function delete(string $uri, array $payload = []): mixed
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make request to Forge servers and return the response.
     */
    protected function request(string $verb, string $uri, array $payload = []): mixed
    {
        $payload = empty($payload) ? [] : ['json' => $payload];

        $response = $this->guzzle->request($verb, $uri, $payload);

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode > 299) {
            return $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    /**
     * Handle the request error.
     *
     * @throws \Exception
     * @throws \Laravel\Forge\Exceptions\FailedActionException
     * @throws \Laravel\Forge\Exceptions\ForbiddenException
     * @throws \Laravel\Forge\Exceptions\NotFoundException
     * @throws \Laravel\Forge\Exceptions\ValidationException
     * @throws \Laravel\Forge\Exceptions\RateLimitExceededException
     */
    protected function handleRequestError(ResponseInterface $response): never
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() === 403) {
            throw new ForbiddenException((string) $response->getBody());
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException;
        }

        if ($response->getStatusCode() == 400) {
            throw new FailedActionException((string) $response->getBody());
        }

        if ($response->getStatusCode() === 429) {
            throw new RateLimitExceededException(
                $response->hasHeader('x-ratelimit-reset')
                    ? (int) $response->getHeader('x-ratelimit-reset')[0]
                    : null
            );
        }

        throw new Exception((string) $response->getBody());
    }

    /**
     * Retry the callback or fail after x seconds.
     *
     * @throws \Laravel\Forge\Exceptions\TimeoutException
     */
    public function retry(int $timeout, callable $callback, int $sleep = 5): mixed
    {
        $start = time();

        beginning:

        if ($output = $callback()) {
            return $output;
        }

        if (time() - $start < $timeout) {
            sleep($sleep);

            goto beginning;
        }

        if ($output === null || $output === false) {
            $output = [];
        }

        if (! is_array($output)) {
            $output = [$output];
        }

        throw new TimeoutException($output);
    }
}
