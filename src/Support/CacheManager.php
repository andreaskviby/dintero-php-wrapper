<?php

declare(strict_types=1);

namespace Dintero\Support;

use Psr\SimpleCache\CacheInterface;

/**
 * Cache manager for Dintero API responses
 */
class CacheManager
{
    private ?CacheInterface $cache = null;
    private int $defaultTtl = 3600; // 1 hour
    private array $cacheConfig = [];

    public function __construct(?CacheInterface $cache = null, array $config = [])
    {
        $this->cache = $cache;
        $this->cacheConfig = $config;
        $this->defaultTtl = $config['default_ttl'] ?? 3600;
    }

    /**
     * Get cached data
     */
    public function get(string $key, $default = null)
    {
        if (!$this->isEnabled()) {
            return $default;
        }

        return $this->cache->get($this->formatKey($key), $default);
    }

    /**
     * Set cached data
     */
    public function set(string $key, $value, ?int $ttl = null): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $ttl = $ttl ?? $this->getTtlForKey($key);
        return $this->cache->set($this->formatKey($key), $value, $ttl);
    }

    /**
     * Delete cached data
     */
    public function delete(string $key): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->cache->delete($this->formatKey($key));
    }

    /**
     * Check if cache is enabled
     */
    public function isEnabled(): bool
    {
        return $this->cache !== null;
    }

    /**
     * Cache API response
     */
    public function cacheApiResponse(string $endpoint, array $params, array $response, ?int $ttl = null): bool
    {
        $key = $this->buildApiCacheKey($endpoint, $params);
        return $this->set($key, $response, $ttl);
    }

    /**
     * Get cached API response
     */
    public function getCachedApiResponse(string $endpoint, array $params): ?array
    {
        $key = $this->buildApiCacheKey($endpoint, $params);
        return $this->get($key);
    }

    /**
     * Invalidate API response cache
     */
    public function invalidateApiResponse(string $endpoint, array $params = []): bool
    {
        $key = $this->buildApiCacheKey($endpoint, $params);
        return $this->delete($key);
    }

    /**
     * Cache customer data
     */
    public function cacheCustomer(string $customerId, array $customerData): bool
    {
        return $this->set("customer:{$customerId}", $customerData, $this->getTtlForResource('customer'));
    }

    /**
     * Get cached customer data
     */
    public function getCachedCustomer(string $customerId): ?array
    {
        return $this->get("customer:{$customerId}");
    }

    /**
     * Invalidate customer cache
     */
    public function invalidateCustomer(string $customerId): bool
    {
        return $this->delete("customer:{$customerId}");
    }

    /**
     * Cache payment methods
     */
    public function cachePaymentMethods(array $paymentMethods): bool
    {
        return $this->set('payment_methods', $paymentMethods, $this->getTtlForResource('payment_methods'));
    }

    /**
     * Get cached payment methods
     */
    public function getCachedPaymentMethods(): ?array
    {
        return $this->get('payment_methods');
    }

    /**
     * Cache exchange rates
     */
    public function cacheExchangeRates(array $rates): bool
    {
        return $this->set('exchange_rates', $rates, $this->getTtlForResource('exchange_rates'));
    }

    /**
     * Get cached exchange rates
     */
    public function getCachedExchangeRates(): ?array
    {
        return $this->get('exchange_rates');
    }

    /**
     * Cache settings
     */
    public function cacheSettings(array $settings): bool
    {
        return $this->set('settings', $settings, $this->getTtlForResource('settings'));
    }

    /**
     * Get cached settings
     */
    public function getCachedSettings(): ?array
    {
        return $this->get('settings');
    }

    /**
     * Invalidate all cache
     */
    public function clear(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->cache->clear();
    }

    /**
     * Tag-based cache invalidation
     */
    public function invalidateByTag(string $tag): bool
    {
        // Implementation would depend on cache backend supporting tags
        // For now, we'll invalidate specific known patterns
        $patterns = $this->getInvalidationPatterns($tag);
        
        foreach ($patterns as $pattern) {
            $this->delete($pattern);
        }

        return true;
    }

    /**
     * Get cache statistics
     */
    public function getStatistics(): array
    {
        // Basic statistics - would be expanded based on cache backend
        return [
            'enabled' => $this->isEnabled(),
            'default_ttl' => $this->defaultTtl,
            'config' => $this->cacheConfig,
        ];
    }

    /**
     * Format cache key with prefix
     */
    private function formatKey(string $key): string
    {
        $prefix = $this->cacheConfig['prefix'] ?? 'dintero';
        return "{$prefix}:{$key}";
    }

    /**
     * Build API cache key
     */
    private function buildApiCacheKey(string $endpoint, array $params): string
    {
        $endpoint = trim($endpoint, '/');
        $paramHash = empty($params) ? '' : ':' . md5(serialize($params));
        return "api:{$endpoint}{$paramHash}";
    }

    /**
     * Get TTL for specific resource type
     */
    private function getTtlForResource(string $resource): int
    {
        $ttlMapping = [
            'customer' => 1800,        // 30 minutes
            'payment_methods' => 3600,  // 1 hour
            'exchange_rates' => 300,    // 5 minutes
            'settings' => 7200,         // 2 hours
            'reports' => 600,           // 10 minutes
        ];

        return $ttlMapping[$resource] ?? $this->defaultTtl;
    }

    /**
     * Get TTL for specific cache key
     */
    private function getTtlForKey(string $key): int
    {
        // Determine TTL based on key pattern
        if (strpos($key, 'customer:') === 0) {
            return $this->getTtlForResource('customer');
        }
        
        if (strpos($key, 'api:') === 0) {
            return $this->getTtlForResource('reports');
        }

        return $this->defaultTtl;
    }

    /**
     * Get invalidation patterns for tag
     */
    private function getInvalidationPatterns(string $tag): array
    {
        $patterns = [
            'customer' => ['customer:*'],
            'payment' => ['api:payment*', 'api:transactions*'],
            'settings' => ['settings', 'api:settings*'],
            'reports' => ['api:reports*'],
        ];

        return $patterns[$tag] ?? [];
    }

    /**
     * Should cache this endpoint
     */
    public function shouldCache(string $endpoint, string $method = 'GET'): bool
    {
        // Only cache GET requests
        if ($method !== 'GET') {
            return false;
        }

        // Define cacheable endpoints
        $cacheableEndpoints = [
            '/customers/',
            '/payment-methods',
            '/settings/',
            '/reports/',
            '/profiles/',
        ];

        foreach ($cacheableEndpoints as $cacheableEndpoint) {
            if (strpos($endpoint, $cacheableEndpoint) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cache key for request
     */
    public function getRequestCacheKey(string $method, string $endpoint, array $params = []): string
    {
        return $this->buildApiCacheKey("{$method}:{$endpoint}", $params);
    }
}