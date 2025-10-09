<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit\Http;

use Dintero\Http\HttpClient;
use Dintero\Support\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class HttpClientTest extends TestCase
{
    public function test_http_client_uses_base_url_with_trailing_slash()
    {
        $config = new Configuration([
            'environment' => 'production',
            'api_key' => 'test_key',
        ]);

        $httpClient = new HttpClient($config);
        
        // Use reflection to access the private client property
        $reflection = new ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        
        /** @var Client $guzzleClient */
        $guzzleClient = $clientProperty->getValue($httpClient);
        $baseUri = (string) $guzzleClient->getConfig('base_uri');
        
        $this->assertStringEndsWith('/', $baseUri);
        $this->assertEquals('https://api.dintero.com/v1/', $baseUri);
    }

    public function test_http_client_uses_sandbox_base_url_with_trailing_slash()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $httpClient = new HttpClient($config);
        
        // Use reflection to access the private client property
        $reflection = new ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        
        /** @var Client $guzzleClient */
        $guzzleClient = $clientProperty->getValue($httpClient);
        $baseUri = (string) $guzzleClient->getConfig('base_uri');
        
        $this->assertStringEndsWith('/', $baseUri);
        $this->assertEquals('https://api.sandbox.dintero.com/v1/', $baseUri);
    }

    public function test_endpoint_path_construction_with_trailing_slash()
    {
        // Mock handler to capture the actual request URI
        $mockHandler = new MockHandler([
            new Response(200, [], '{"success": true}'),
        ]);
        
        $handlerStack = HandlerStack::create($mockHandler);
        
        // Capture the request to verify the URL
        $requestCapture = null;
        $handlerStack->push(function ($handler) use (&$requestCapture) {
            return function ($request, $options) use ($handler, &$requestCapture) {
                $requestCapture = $request;
                return $handler($request, $options);
            };
        });

        $config = new Configuration([
            'environment' => 'production',
            'api_key' => 'test_key',
        ]);

        // Create HttpClient but manually inject our mock Guzzle client
        $httpClient = new HttpClient($config);
        
        // Use reflection to replace the client with our mocked one
        $reflection = new ReflectionClass($httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        
        $mockClient = new Client([
            'base_uri' => 'https://api.dintero.com/v1/',
            'handler' => $handlerStack,
        ]);
        
        $clientProperty->setValue($httpClient, $mockClient);

        // Make a test request
        $httpClient->get('accounts/T11115430/auth/token');

        // Verify the constructed URL includes v1
        $this->assertNotNull($requestCapture);
        $uri = (string) $requestCapture->getUri();
        $this->assertEquals('https://api.dintero.com/v1/accounts/T11115430/auth/token', $uri);
    }
}