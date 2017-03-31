<?php


class ForgeSdkTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_making_basic_requests()
    {
        $forge = new \Themsaid\Forge\Forge('123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn('{"recipes": [{"key": "value"}]}');

        $forge->recipes();
    }

    public function test_handling_validation_errors()
    {
        $forge = new \Themsaid\Forge\Forge('123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(422);
        $response->shouldReceive('getBody')->once()->andReturn('{"name": ["The name is required."]}');

        try {
            $forge->recipes();
        } catch (\Themsaid\Forge\Exceptions\ValidationException $e) {
        }

        $this->assertEquals(['name' => ['The name is required.']], $e->errors());
    }

    public function test_handling_404_errors()
    {
        $this->expectException(\Themsaid\Forge\Exceptions\NotFoundException::class);

        $forge = new \Themsaid\Forge\Forge('123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(404);

        $forge->recipes();
    }

    public function test_handling_failed_action_errors()
    {
        $forge = new \Themsaid\Forge\Forge('123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'recipes', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(400);
        $response->shouldReceive('getBody')->once()->andReturn('Error!');

        try {
            $forge->recipes();
        } catch (\Themsaid\Forge\Exceptions\FailedActionException $e) {
        }

        $this->assertEquals('Error!', $e->getMessage());
    }
}