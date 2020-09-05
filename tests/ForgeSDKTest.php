<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Laravel\Forge\Exceptions\FailedActionException;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Exceptions\ValidationException;
use Laravel\Forge\Forge;
use Mockery;
use PHPUnit\Framework\TestCase;

class ForgeSDKTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_making_basic_requests()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn('{"recipes": [{"key": "value"}]}');

        $this->assertCount(1, $forge->recipes());
    }

    public function test_handling_validation_errors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(422);
        $response->shouldReceive('getBody')->once()->andReturn('{"name": ["The name is required."]}');

        try {
            $forge->recipes();
        } catch (ValidationException $e) {
        }

        $this->assertEquals(['name' => ['The name is required.']], $e->errors());
    }

    public function test_handling_404_errors()
    {
        $this->expectException(NotFoundException::class);

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(404);

        $forge->recipes();
    }

    public function test_handling_failed_action_errors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(400);
        $response->shouldReceive('getBody')->once()->andReturn('Error!');

        try {
            $forge->recipes();
        } catch (FailedActionException $e) {
            $this->assertSame('Error!', $e->getMessage());
        }
    }
}
