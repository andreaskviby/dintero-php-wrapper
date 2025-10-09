<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit\Http;

use Dintero\Http\HttpClient;
use Dintero\Support\Configuration;
use Dintero\Exceptions\AuthenticationException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    private array $requestHistory = [];

    public function test_headers_include_content_type_json()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $httpClient = new HttpClient($config);
        
        // Use reflection to access the private getHeaders method
        $reflection = new \ReflectionClass($httpClient);
        $getHeadersMethod = $reflection->getMethod('getHeaders');
        $getHeadersMethod->setAccessible(true);
        
        $headers = $getHeadersMethod->invoke($httpClient);
        
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertEquals('application/json', $headers['Accept']);
    }

    public function test_post_request_sends_json_content_type()
    {
        $mockHandler = MockHandler::createWithMiddleware([
            new Response(200, [], '{"data": "test"}')
        ]);
        
        $history = Middleware::history($this->requestHistory);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        // Use reflection to replace the Guzzle client with our mock
        $httpClient = new HttpClient($config);
        $reflection = new \ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($httpClient, new Client(['handler' => $stack]));

        $httpClient->post('/test', ['key' => 'value']);

        $this->assertCount(1, $this->requestHistory);
        $request = $this->requestHistory[0]['request'];
        
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('application/json', $request->getHeaderLine('Accept'));
    }

    public function test_authentication_includes_audience_when_configured()
    {
        $mockHandler = MockHandler::createWithMiddleware([
            new Response(200, [], '{"access_token": "test_token"}')
        ]);
        
        $history = Middleware::history($this->requestHistory);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $config = new Configuration([
            'environment' => 'sandbox',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'audience' => 'https://api.dintero.com',
        ]);

        $httpClient = new HttpClient($config);
        $reflection = new \ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($httpClient, new Client(['handler' => $stack]));

        $httpClient->authenticate();

        $this->assertCount(1, $this->requestHistory);
        $request = $this->requestHistory[0]['request'];
        
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertArrayHasKey('audience', $body);
        $this->assertEquals('https://api.dintero.com', $body['audience']);
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function test_authentication_without_audience()
    {
        $mockHandler = MockHandler::createWithMiddleware([
            new Response(200, [], '{"access_token": "test_token"}')
        ]);
        
        $history = Middleware::history($this->requestHistory);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $config = new Configuration([
            'environment' => 'sandbox',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        $httpClient = new HttpClient($config);
        $reflection = new \ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($httpClient, new Client(['handler' => $stack]));

        $httpClient->authenticate();

        $this->assertCount(1, $this->requestHistory);
        $request = $this->requestHistory[0]['request'];
        
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertArrayNotHasKey('audience', $body);
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
    }

    protected function setUp(): void
    {
        $this->requestHistory = [];
    }
}