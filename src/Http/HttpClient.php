<?php

declare(strict_types=1);

namespace Dintero\Http;

use Dintero\Contracts\HttpClientInterface;
use Dintero\Contracts\HttpResponseInterface;
use Dintero\Exceptions\DinteroException;
use Dintero\Exceptions\AuthenticationException;
use Dintero\Exceptions\ValidationException;
use Dintero\Exceptions\RateLimitException;
use Dintero\Support\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Client for Dintero API
 */
class HttpClient implements HttpClientInterface
{
    private Client $client;
    private Configuration $config;
    private ?string $accessToken = null;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config->getBaseUrl(),
            'timeout' => $config->get('timeout', 30),
            'headers' => [
                'User-Agent' => $config->get('user_agent', 'Dintero PHP Wrapper/1.0'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint, array $query = []): HttpResponseInterface
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    public function post(string $endpoint, array $data = []): HttpResponseInterface
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    public function put(string $endpoint, array $data = []): HttpResponseInterface
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    public function patch(string $endpoint, array $data = []): HttpResponseInterface
    {
        return $this->request('PATCH', $endpoint, ['json' => $data]);
    }

    public function delete(string $endpoint): HttpResponseInterface
    {
        return $this->request('DELETE', $endpoint);
    }

    private function request(string $method, string $endpoint, array $options = []): HttpResponseInterface
    {
        $endpoint = ltrim($endpoint, '/');
        $options = array_merge($options, [
            'headers' => $this->getHeaders(),
        ]);

        $retryAttempts = $this->config->get('retry_attempts', 3);
        $retryDelay = $this->config->get('retry_delay', 1000);

        for ($attempt = 0; $attempt <= $retryAttempts; $attempt++) {
            try {
                $response = $this->client->request($method, $endpoint, $options);
                return $this->createResponse($response);
            } catch (RequestException $e) {
                if ($attempt === $retryAttempts || !$this->shouldRetry($e)) {
                    $this->handleException($e);
                }
                
                if ($attempt < $retryAttempts) {
                    usleep($retryDelay * 1000 * ($attempt + 1)); // Exponential backoff
                }
            } catch (GuzzleException $e) {
                throw new DinteroException('HTTP request failed: ' . $e->getMessage(), 0, $e);
            }
        }

        throw new DinteroException('Max retry attempts exceeded');
    }

    private function getHeaders(): array
    {
        $headers = [];

        // Add authentication
        if ($this->config->getApiKey()) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getApiKey();
        } elseif ($this->accessToken) {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken;
        }

        return $headers;
    }

    private function createResponse(ResponseInterface $response): HttpResponseInterface
    {
        return new HttpResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()->getContents()
        );
    }

    private function shouldRetry(RequestException $e): bool
    {
        $statusCode = $e->getResponse()?->getStatusCode();
        
        // Retry on server errors and rate limiting
        return in_array($statusCode, [429, 500, 502, 503, 504]);
    }

    private function handleException(RequestException $e): void
    {
        $response = $e->getResponse();
        $statusCode = $response?->getStatusCode() ?? 0;
        $body = $response?->getBody()->getContents() ?? '';

        $errorData = [];
        if (!empty($body)) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $errorData = $decoded;
            }
        }

        // Extract error message from Dintero's error format
        $message = $e->getMessage();
        if (isset($errorData['error']['message'])) {
            $message = $errorData['error']['message'];
        } elseif (isset($errorData['error']) && is_string($errorData['error'])) {
            $message = $errorData['error'];
        } elseif (isset($errorData['message'])) {
            $message = $errorData['message'];
        }

        match ($statusCode) {
            401 => throw new AuthenticationException($message, $statusCode, $e),
            422 => throw new ValidationException($message, $statusCode, $e, $errorData),
            429 => throw new RateLimitException($message, $statusCode, $e),
            default => throw new DinteroException($message, $statusCode, $e),
        };
    }

    /**
     * Authenticate using OAuth2 client credentials
     */
    public function authenticate(): void
    {
        if (!$this->config->getClientId() || !$this->config->getClientSecret()) {
            throw new AuthenticationException('Client ID and Secret are required for OAuth authentication');
        }

        try {
            $response = $this->client->post('oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config->getClientId(),
                    'client_secret' => $this->config->getClientSecret(),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $data['access_token'] ?? null;

            if (!$this->accessToken) {
                throw new AuthenticationException('Failed to obtain access token');
            }
        } catch (GuzzleException $e) {
            throw new AuthenticationException('OAuth authentication failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }
}