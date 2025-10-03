<?php

declare(strict_types=1);

namespace Dintero\Support;

use Dintero\Exceptions\RateLimitException;

/**
 * Rate limit handler for API requests
 */
class RateLimitHandler
{
    private array $config;
    private array $requestCounts = [];
    private array $lastResetTime = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'max_requests_per_minute' => 100,
            'max_requests_per_hour' => 1000,
            'backoff_strategy' => 'exponential', // exponential, linear, fixed
            'max_backoff_time' => 300, // 5 minutes
            'initial_backoff_time' => 1,
        ], $config);
    }

    /**
     * Check if request is allowed
     */
    public function isRequestAllowed(): bool
    {
        $this->cleanupOldEntries();
        
        return $this->checkMinuteLimit() && $this->checkHourLimit();
    }

    /**
     * Record a request
     */
    public function recordRequest(): void
    {
        $now = time();
        
        // Record for minute limit
        $minuteKey = $this->getMinuteKey($now);
        $this->requestCounts[$minuteKey] = ($this->requestCounts[$minuteKey] ?? 0) + 1;
        
        // Record for hour limit
        $hourKey = $this->getHourKey($now);
        $this->requestCounts[$hourKey] = ($this->requestCounts[$hourKey] ?? 0) + 1;
    }

    /**
     * Handle rate limit exceeded
     */
    public function handleRateLimitExceeded(int $retryAfter = 0): void
    {
        if ($retryAfter === 0) {
            $retryAfter = $this->calculateBackoffTime();
        }

        throw new RateLimitException(
            "Rate limit exceeded. Retry after {$retryAfter} seconds.",
            $retryAfter
        );
    }

    /**
     * Get retry delay based on backoff strategy
     */
    public function getRetryDelay(int $attemptNumber = 1): int
    {
        switch ($this->config['backoff_strategy']) {
            case 'exponential':
                return min(
                    $this->config['initial_backoff_time'] * (2 ** ($attemptNumber - 1)),
                    $this->config['max_backoff_time']
                );
                
            case 'linear':
                return min(
                    $this->config['initial_backoff_time'] * $attemptNumber,
                    $this->config['max_backoff_time']
                );
                
            case 'fixed':
            default:
                return $this->config['initial_backoff_time'];
        }
    }

    /**
     * Parse rate limit headers
     */
    public function parseRateLimitHeaders(array $headers): array
    {
        $rateLimitInfo = [
            'limit' => null,
            'remaining' => null,
            'reset' => null,
            'retry_after' => null,
        ];

        // Common rate limit header patterns
        $headerMappings = [
            'x-ratelimit-limit' => 'limit',
            'x-ratelimit-remaining' => 'remaining',
            'x-ratelimit-reset' => 'reset',
            'retry-after' => 'retry_after',
            'x-rate-limit-limit' => 'limit',
            'x-rate-limit-remaining' => 'remaining',
            'x-rate-limit-reset' => 'reset',
        ];

        foreach ($headers as $headerName => $headerValue) {
            $lowerHeaderName = strtolower($headerName);
            if (isset($headerMappings[$lowerHeaderName])) {
                $rateLimitInfo[$headerMappings[$lowerHeaderName]] = is_array($headerValue) 
                    ? (int) $headerValue[0] 
                    : (int) $headerValue;
            }
        }

        return $rateLimitInfo;
    }

    /**
     * Update rate limit info from response headers
     */
    public function updateFromHeaders(array $headers): void
    {
        $rateLimitInfo = $this->parseRateLimitHeaders($headers);
        
        if ($rateLimitInfo['remaining'] !== null && $rateLimitInfo['remaining'] <= 0) {
            $retryAfter = $rateLimitInfo['retry_after'] ?? $this->calculateBackoffTime();
            $this->handleRateLimitExceeded($retryAfter);
        }
    }

    /**
     * Get current rate limit status
     */
    public function getStatus(): array
    {
        $this->cleanupOldEntries();
        
        $now = time();
        $minuteKey = $this->getMinuteKey($now);
        $hourKey = $this->getHourKey($now);
        
        return [
            'requests_this_minute' => $this->requestCounts[$minuteKey] ?? 0,
            'requests_this_hour' => $this->requestCounts[$hourKey] ?? 0,
            'minute_limit' => $this->config['max_requests_per_minute'],
            'hour_limit' => $this->config['max_requests_per_hour'],
            'minute_remaining' => max(0, $this->config['max_requests_per_minute'] - ($this->requestCounts[$minuteKey] ?? 0)),
            'hour_remaining' => max(0, $this->config['max_requests_per_hour'] - ($this->requestCounts[$hourKey] ?? 0)),
        ];
    }

    /**
     * Reset rate limit counters
     */
    public function reset(): void
    {
        $this->requestCounts = [];
        $this->lastResetTime = [];
    }

    /**
     * Check minute limit
     */
    private function checkMinuteLimit(): bool
    {
        $now = time();
        $minuteKey = $this->getMinuteKey($now);
        $count = $this->requestCounts[$minuteKey] ?? 0;
        
        return $count < $this->config['max_requests_per_minute'];
    }

    /**
     * Check hour limit
     */
    private function checkHourLimit(): bool
    {
        $now = time();
        $hourKey = $this->getHourKey($now);
        $count = $this->requestCounts[$hourKey] ?? 0;
        
        return $count < $this->config['max_requests_per_hour'];
    }

    /**
     * Get minute key for tracking
     */
    private function getMinuteKey(int $timestamp): string
    {
        return 'minute_' . intval($timestamp / 60);
    }

    /**
     * Get hour key for tracking
     */
    private function getHourKey(int $timestamp): string
    {
        return 'hour_' . intval($timestamp / 3600);
    }

    /**
     * Calculate backoff time
     */
    private function calculateBackoffTime(): int
    {
        $now = time();
        
        // If we're hitting minute limit, wait until next minute
        $minuteKey = $this->getMinuteKey($now);
        if (($this->requestCounts[$minuteKey] ?? 0) >= $this->config['max_requests_per_minute']) {
            return 60 - ($now % 60);
        }
        
        // If we're hitting hour limit, use configured backoff
        return $this->getRetryDelay();
    }

    /**
     * Clean up old entries
     */
    private function cleanupOldEntries(): void
    {
        $now = time();
        $currentMinute = intval($now / 60);
        $currentHour = intval($now / 3600);
        
        // Keep only current and previous minute/hour
        foreach ($this->requestCounts as $key => $count) {
            if (strpos($key, 'minute_') === 0) {
                $keyTime = (int) str_replace('minute_', '', $key);
                if ($keyTime < $currentMinute - 1) {
                    unset($this->requestCounts[$key]);
                }
            } elseif (strpos($key, 'hour_') === 0) {
                $keyTime = (int) str_replace('hour_', '', $key);
                if ($keyTime < $currentHour - 1) {
                    unset($this->requestCounts[$key]);
                }
            }
        }
    }

    /**
     * Wait for rate limit reset
     */
    public function waitForReset(): void
    {
        $retryAfter = $this->calculateBackoffTime();
        
        if ($retryAfter > 0) {
            sleep($retryAfter);
        }
    }

    /**
     * Should retry request
     */
    public function shouldRetry(int $attemptNumber, int $maxAttempts = 3): bool
    {
        return $attemptNumber <= $maxAttempts;
    }

    /**
     * Get configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Update configuration
     */
    public function updateConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }
}