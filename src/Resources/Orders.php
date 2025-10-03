<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Orders resource
 */
class Orders extends BaseResource
{
    /**
     * Create a new order
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/orders', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve an order
     */
    public function get(string $orderId): array
    {
        $response = $this->httpClient->get("/orders/{$orderId}");
        return $response->json();
    }

    /**
     * Update an order
     */
    public function update(string $orderId, array $data): array
    {
        $response = $this->httpClient->put("/orders/{$orderId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Cancel an order
     */
    public function cancel(string $orderId): array
    {
        $response = $this->httpClient->post("/orders/{$orderId}/cancel");
        return $response->json();
    }

    /**
     * List orders
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/orders', $params);
        return $response->json();
    }

    /**
     * Get all orders (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/orders', $params);
    }

    /**
     * Get order items
     */
    public function getItems(string $orderId): array
    {
        $response = $this->httpClient->get("/orders/{$orderId}/items");
        return $response->json();
    }

    /**
     * Add item to order
     */
    public function addItem(string $orderId, array $itemData): array
    {
        $response = $this->httpClient->post("/orders/{$orderId}/items", $itemData);
        return $response->json();
    }

    /**
     * Update order item
     */
    public function updateItem(string $orderId, string $itemId, array $itemData): array
    {
        $response = $this->httpClient->put("/orders/{$orderId}/items/{$itemId}", $itemData);
        return $response->json();
    }

    /**
     * Remove item from order
     */
    public function removeItem(string $orderId, string $itemId): void
    {
        $this->httpClient->delete("/orders/{$orderId}/items/{$itemId}");
    }

    /**
     * Create order with items
     */
    public function createWithItems(array $orderData, array $items): array
    {
        $orderData['items'] = $items;
        return $this->create($orderData);
    }

    /**
     * Get order shipping information
     */
    public function getShipping(string $orderId): array
    {
        $response = $this->httpClient->get("/orders/{$orderId}/shipping");
        return $response->json();
    }

    /**
     * Update order shipping information
     */
    public function updateShipping(string $orderId, array $shippingData): array
    {
        $response = $this->httpClient->put("/orders/{$orderId}/shipping", $shippingData);
        return $response->json();
    }
}