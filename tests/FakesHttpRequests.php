<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait FakesHttpRequests
{
    protected ?MockHandler $mockHandler = null;

    /**
     * Create a Guzzle client which responds with the expected requests, in order.
     */
    protected function fakeHttpClient(): Client
    {
        $this->mockHandler = new MockHandler;

        return new Client(['handler' => $this->mockHandler]);
    }

    /**
     * Expect the next request to match the given method, URI, and options, and respond with the given response.
     */
    protected function expectRequest(string $method, string $uri, array $options, ResponseInterface $response): void
    {
        $this->mockHandler->append(function (RequestInterface $request) use ($method, $uri, $options, $response) {
            $this->assertSame($method, $request->getMethod());
            $this->assertSame($uri, $request->getUri()->getPath());
            $this->assertEquals($options, $this->requestOptions($request));

            return $response;
        });
    }

    /**
     * Rebuild the Guzzle request options sent with the given request.
     */
    protected function requestOptions(RequestInterface $request): array
    {
        $options = [];

        $body = (string) $request->getBody();

        if ($body !== '') {
            $options['json'] = json_decode($body, true);
        }

        $query = $request->getUri()->getQuery();

        if ($query !== '') {
            parse_str($query, $options['query']);
        }

        return $options;
    }

    protected function assertPostConditions(): void
    {
        if ($this->mockHandler) {
            $this->assertCount(0, $this->mockHandler, 'Not all expected requests were made.');
        }
    }
}
