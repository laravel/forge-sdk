<?php

namespace Laravel\Forge;

use Laravel\Forge\Exceptions\TimeoutException;
use Psr\Http\Message\ResponseInterface;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Exceptions\ValidationException;
use Laravel\Forge\Exceptions\FailedActionException;

trait MakesHttpRequests
{
    /**
     * Make a GET request to Forge servers and return the response.
     *
     * @param  string $uri
     * @return mixed
     */
    private function get($uri)
    {
        return $this->request('GET', $uri);
    }

    /**
     * Make a POST request to Forge servers and return the response.
     *
     * @param  string $uri
     * @param  array $payload
     * @return mixed
     */
    private function post($uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Forge servers and return the response.
     *
     * @param  string $uri
     * @param  array $payload
     * @return mixed
     */
    private function put($uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a DELETE request to Forge servers and return the response.
     *
     * @param  string $uri
     * @param  array $payload
     * @return mixed
     */
    private function delete($uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make request to Forge servers and return the response.
     *
     * @param  string $verb
     * @param  string $uri
     * @param  array $payload
     * @return mixed
     */
    private function request($verb, $uri, array $payload = [])
    {
        $response = $this->guzzle->request($verb, $uri,
            empty($payload) ? [] : ['form_params' => $payload]
        );

        if ($response->getStatusCode() != 200) {
            return $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    /**
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    private function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException();
        }

        if ($response->getStatusCode() == 400) {
            throw new FailedActionException((string) $response->getBody());
        }

        throw new \Exception((string) $response->getBody());
    }

    /**
     * Retry the callback or fail after x seconds.
     *
     * @param  integer $timeout
     * @param  callable $callback
     * @return mixed
     */
    public function retry($timeout, $callback)
    {
        $start = time();

        beginning:

        if ($output = $callback()) {
            return $output;
        }

        if (time() - $start < $timeout) {
            sleep(5);

            goto beginning;
        }

        throw new TimeoutException($output);
    }
}