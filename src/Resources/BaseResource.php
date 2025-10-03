<?php

declare(strict_types=1);

namespace Dintero\Resources;

use Dintero\Http\HttpClient;

/**
 * Base resource class
 */
abstract class BaseResource
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Build URL with parameters
     */
    protected function buildUrl(string $path, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string) $value, $path);
        }
        return $path;
    }

    /**
     * Clean and prepare data for API requests
     */
    protected function prepareData(array $data): array
    {
        return array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Paginate through all results
     */
    protected function paginate(string $endpoint, array $params = []): \Generator
    {
        $page = 1;
        $limit = $params['limit'] ?? 100;

        do {
            $response = $this->httpClient->get($endpoint, array_merge($params, [
                'page' => $page,
                'limit' => $limit,
            ]));

            $data = $response->json();
            $items = $data['data'] ?? $data['items'] ?? [];

            foreach ($items as $item) {
                yield $item;
            }

            $hasMore = !empty($items) && count($items) === $limit;
            $page++;
        } while ($hasMore);
    }
}