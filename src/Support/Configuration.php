<?php

declare(strict_types=1);

namespace Dintero\Support;

use Dintero\Exceptions\ConfigurationException;

/**
 * Configuration management for Dintero client
 */
class Configuration
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaults(), $config);
        $this->validate();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        data_set($this->config, $key, $value);
    }

    public function all(): array
    {
        return $this->config;
    }

    public function getApiKey(): ?string
    {
        return $this->get('api_key');
    }

    public function getClientId(): ?string
    {
        return $this->get('client_id');
    }

    public function getClientSecret(): ?string
    {
        return $this->get('client_secret');
    }

    public function getBaseUrl(): string
    {
        return $this->get('base_url');
    }

    public function getEnvironment(): string
    {
        return $this->get('environment');
    }

    public function isProduction(): bool
    {
        return $this->getEnvironment() === 'production';
    }

    public function isSandbox(): bool
    {
        return $this->getEnvironment() === 'sandbox';
    }

    private function getDefaults(): array
    {
        return [
            'environment' => env('DINTERO_ENVIRONMENT', 'sandbox'),
            'api_key' => env('DINTERO_API_KEY'),
            'client_id' => env('DINTERO_CLIENT_ID'),
            'client_secret' => env('DINTERO_CLIENT_SECRET'),
            'base_url' => env('DINTERO_BASE_URL', 'https://api.dintero.com/v1/'),
            'sandbox_base_url' => env('DINTERO_SANDBOX_BASE_URL', 'https://api.sandbox.dintero.com/v1/'),
            'timeout' => env('DINTERO_TIMEOUT', 30),
            'retry_attempts' => env('DINTERO_RETRY_ATTEMPTS', 3),
            'retry_delay' => env('DINTERO_RETRY_DELAY', 1000), // milliseconds
            'log_requests' => env('DINTERO_LOG_REQUESTS', false),
            'log_responses' => env('DINTERO_LOG_RESPONSES', false),
            'verify_webhooks' => env('DINTERO_VERIFY_WEBHOOKS', true),
            'webhook_secret' => env('DINTERO_WEBHOOK_SECRET'),
            'user_agent' => 'Dintero PHP Wrapper/1.0',
        ];
    }

    private function validate(): void
    {
        $environment = $this->getEnvironment();
        
        if (!in_array($environment, ['production', 'sandbox'])) {
            throw new ConfigurationException('Environment must be either "production" or "sandbox"');
        }

        // Set the correct base URL based on environment
        if ($environment === 'sandbox') {
            $this->set('base_url', $this->get('sandbox_base_url'));
        }

        // Validate required credentials
        if (empty($this->getApiKey()) && (empty($this->getClientId()) || empty($this->getClientSecret()))) {
            throw new ConfigurationException('Either api_key or both client_id and client_secret must be provided');
        }
    }
}

// Helper function for Laravel-style dot notation access
if (!function_exists('data_get')) {
    function data_get(array $array, string $key, mixed $default = null): mixed
    {
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            foreach ($keys as $segment) {
                if (is_array($array) && array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }
            return $array;
        }

        return $array[$key] ?? $default;
    }
}

if (!function_exists('data_set')) {
    function data_set(array &$array, string $key, mixed $value): void
    {
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $current = &$array;
            
            foreach ($keys as $segment) {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }
                $current = &$current[$segment];
            }
            
            $current = $value;
        } else {
            $array[$key] = $value;
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        // Convert string booleans to actual booleans
        if (is_string($value)) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', '1', 'yes', 'on'])) {
                return true;
            } elseif (in_array($lower, ['false', '0', 'no', 'off'])) {
                return false;
            }
        }
        
        return $value;
    }
}