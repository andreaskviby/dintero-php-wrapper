<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Customers resource
 */
class Customers extends BaseResource
{
    /**
     * Create a new customer
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/customers', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a customer
     */
    public function get(string $customerId): array
    {
        $response = $this->httpClient->get("/customers/{$customerId}");
        return $response->json();
    }

    /**
     * Update a customer
     */
    public function update(string $customerId, array $data): array
    {
        $response = $this->httpClient->put("/customers/{$customerId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a customer
     */
    public function delete(string $customerId): void
    {
        $this->httpClient->delete("/customers/{$customerId}");
    }

    /**
     * List customers
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/customers', $params);
        return $response->json();
    }

    /**
     * Get all customers (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/customers', $params);
    }

    /**
     * Search customers
     */
    public function search(string $query, array $params = []): array
    {
        $params['query'] = $query;
        $response = $this->httpClient->get('/customers/search', $params);
        return $response->json();
    }

    /**
     * Get customer payment methods
     */
    public function getPaymentMethods(string $customerId): array
    {
        $response = $this->httpClient->get("/customers/{$customerId}/payment-methods");
        return $response->json();
    }

    /**
     * Add payment method to customer
     */
    public function addPaymentMethod(string $customerId, array $paymentMethodData): array
    {
        $response = $this->httpClient->post("/customers/{$customerId}/payment-methods", $paymentMethodData);
        return $response->json();
    }

    /**
     * Remove payment method from customer
     */
    public function removePaymentMethod(string $customerId, string $paymentMethodId): void
    {
        $this->httpClient->delete("/customers/{$customerId}/payment-methods/{$paymentMethodId}");
    }

    /**
     * Get customer orders
     */
    public function getOrders(string $customerId, array $params = []): array
    {
        $response = $this->httpClient->get("/customers/{$customerId}/orders", $params);
        return $response->json();
    }
}