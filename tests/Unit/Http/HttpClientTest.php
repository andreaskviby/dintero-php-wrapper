<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit\Http;

use Dintero\Http\HttpClient;
use Dintero\Support\Configuration;
use Dintero\Exceptions\DinteroException;
use Dintero\Exceptions\AuthenticationException;
use Dintero\Exceptions\ValidationException;
use Dintero\Exceptions\RateLimitException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Mockery;

class HttpClientTest extends TestCase
{
    private Configuration $config;
    private MockHandler $mockHandler;
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        
        // Use reflection to inject the mock handler into HttpClient
        $this->httpClient = new HttpClient($this->config);
        $reflection = new \ReflectionClass($this->httpClient);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->httpClient, new Client(['handler' => $handlerStack]));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_handles_dintero_nested_error_format()
    {
        // Simulate Dintero's error response format where 'error' is an object with 'message'
        $errorBody = json_encode([
            'error' => [
                'message' => 'Payment failed due to insufficient funds',
                'code' => 'insufficient_funds',
                'details' => 'Additional error details'
            ]
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Bad Request',
                new Request('POST', '/test'),
                new Response(400, [], $errorBody)
            )
        );

        $this->expectException(DinteroException::class);
        $this->expectExceptionMessage('Payment failed due to insufficient funds');

        $this->httpClient->post('/test', []);
    }

    public function test_handles_dintero_simple_error_format()
    {
        // Simulate simple error format where 'error' is a string
        $errorBody = json_encode([
            'error' => 'Simple error message'
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Bad Request',
                new Request('POST', '/test'),
                new Response(400, [], $errorBody)
            )
        );

        $this->expectException(DinteroException::class);
        $this->expectExceptionMessage('Simple error message');

        $this->httpClient->post('/test', []);
    }

    public function test_handles_standard_message_format()
    {
        // Simulate standard error format with 'message' field
        $errorBody = json_encode([
            'message' => 'Standard error message'
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Bad Request',
                new Request('POST', '/test'),
                new Response(400, [], $errorBody)
            )
        );

        $this->expectException(DinteroException::class);
        $this->expectExceptionMessage('Standard error message');

        $this->httpClient->post('/test', []);
    }

    public function test_falls_back_to_original_exception_message()
    {
        // Simulate malformed or empty error response
        $errorBody = json_encode([]);

        $this->mockHandler->append(
            new RequestException(
                'Original exception message',
                new Request('POST', '/test'),
                new Response(400, [], $errorBody)
            )
        );

        $this->expectException(DinteroException::class);
        $this->expectExceptionMessage('Original exception message');

        $this->httpClient->post('/test', []);
    }

    public function test_throws_authentication_exception_for_401()
    {
        $errorBody = json_encode([
            'error' => [
                'message' => 'Invalid API key'
            ]
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Unauthorized',
                new Request('POST', '/test'),
                new Response(401, [], $errorBody)
            )
        );

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $this->httpClient->post('/test', []);
    }

    public function test_throws_validation_exception_for_422()
    {
        $errorBody = json_encode([
            'error' => [
                'message' => 'Validation failed'
            ],
            'errors' => ['field' => ['Field is required']]
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Unprocessable Entity',
                new Request('POST', '/test'),
                new Response(422, [], $errorBody)
            )
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->httpClient->post('/test', []);
    }

    public function test_throws_rate_limit_exception_for_429()
    {
        $errorBody = json_encode([
            'error' => [
                'message' => 'Rate limit exceeded'
            ]
        ]);

        $this->mockHandler->append(
            new RequestException(
                'Too Many Requests',
                new Request('POST', '/test'),
                new Response(429, [], $errorBody)
            )
        );

        $this->expectException(RateLimitException::class);
        $this->expectExceptionMessage('Rate limit exceeded');

        $this->httpClient->post('/test', []);
    }
}