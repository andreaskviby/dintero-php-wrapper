<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Billing resource for subscriptions and recurring payments
 */
class Billing extends BaseResource
{
    /**
     * Create a subscription
     */
    public function createSubscription(array $data): array
    {
        $response = $this->httpClient->post('/billing/subscriptions', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a subscription
     */
    public function getSubscription(string $subscriptionId): array
    {
        $response = $this->httpClient->get("/billing/subscriptions/{$subscriptionId}");
        return $response->json();
    }

    /**
     * Update a subscription
     */
    public function updateSubscription(string $subscriptionId, array $data): array
    {
        $response = $this->httpClient->put("/billing/subscriptions/{$subscriptionId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId, array $options = []): array
    {
        $data = [
            'cancel_at_period_end' => $options['cancel_at_period_end'] ?? true,
            'reason' => $options['reason'] ?? null,
        ];

        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/cancel", $data);
        return $response->json();
    }

    /**
     * Pause a subscription
     */
    public function pauseSubscription(string $subscriptionId, array $options = []): array
    {
        $data = [
            'pause_collection' => $options['pause_collection'] ?? true,
            'resume_at' => $options['resume_at'] ?? null,
        ];

        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/pause", $data);
        return $response->json();
    }

    /**
     * Resume a subscription
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/resume");
        return $response->json();
    }

    /**
     * List subscriptions
     */
    public function listSubscriptions(array $params = []): array
    {
        $response = $this->httpClient->get('/billing/subscriptions', $params);
        return $response->json();
    }

    /**
     * Get all subscriptions (paginated)
     */
    public function allSubscriptions(array $params = []): \Generator
    {
        return $this->paginate('/billing/subscriptions', $params);
    }

    /**
     * Get subscription invoices
     */
    public function getSubscriptionInvoices(string $subscriptionId, array $params = []): array
    {
        $response = $this->httpClient->get("/billing/subscriptions/{$subscriptionId}/invoices", $params);
        return $response->json();
    }

    /**
     * Create an invoice
     */
    public function createInvoice(array $data): array
    {
        $response = $this->httpClient->post('/billing/invoices', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve an invoice
     */
    public function getInvoice(string $invoiceId): array
    {
        $response = $this->httpClient->get("/billing/invoices/{$invoiceId}");
        return $response->json();
    }

    /**
     * Update an invoice
     */
    public function updateInvoice(string $invoiceId, array $data): array
    {
        $response = $this->httpClient->put("/billing/invoices/{$invoiceId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Send an invoice
     */
    public function sendInvoice(string $invoiceId, array $options = []): array
    {
        $data = [
            'send_email' => $options['send_email'] ?? true,
            'email_subject' => $options['email_subject'] ?? null,
            'email_message' => $options['email_message'] ?? null,
        ];

        $response = $this->httpClient->post("/billing/invoices/{$invoiceId}/send", $data);
        return $response->json();
    }

    /**
     * Pay an invoice
     */
    public function payInvoice(string $invoiceId, array $paymentData = []): array
    {
        $response = $this->httpClient->post("/billing/invoices/{$invoiceId}/pay", $paymentData);
        return $response->json();
    }

    /**
     * Void an invoice
     */
    public function voidInvoice(string $invoiceId, ?string $reason = null): array
    {
        $data = [];
        if ($reason) {
            $data['reason'] = $reason;
        }

        $response = $this->httpClient->post("/billing/invoices/{$invoiceId}/void", $data);
        return $response->json();
    }

    /**
     * List invoices
     */
    public function listInvoices(array $params = []): array
    {
        $response = $this->httpClient->get('/billing/invoices', $params);
        return $response->json();
    }

    /**
     * Get all invoices (paginated)
     */
    public function allInvoices(array $params = []): \Generator
    {
        return $this->paginate('/billing/invoices', $params);
    }

    /**
     * Create a billing plan
     */
    public function createPlan(array $data): array
    {
        $response = $this->httpClient->post('/billing/plans', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a billing plan
     */
    public function getPlan(string $planId): array
    {
        $response = $this->httpClient->get("/billing/plans/{$planId}");
        return $response->json();
    }

    /**
     * Update a billing plan
     */
    public function updatePlan(string $planId, array $data): array
    {
        $response = $this->httpClient->put("/billing/plans/{$planId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a billing plan
     */
    public function deletePlan(string $planId): void
    {
        $this->httpClient->delete("/billing/plans/{$planId}");
    }

    /**
     * List billing plans
     */
    public function listPlans(array $params = []): array
    {
        $response = $this->httpClient->get('/billing/plans', $params);
        return $response->json();
    }

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(string $subscriptionId): string
    {
        $subscription = $this->getSubscription($subscriptionId);
        return $subscription['status'] ?? 'unknown';
    }

    /**
     * Check if subscription is active
     */
    public function isSubscriptionActive(string $subscriptionId): bool
    {
        return $this->getSubscriptionStatus($subscriptionId) === 'active';
    }

    /**
     * Check if subscription is cancelled
     */
    public function isSubscriptionCancelled(string $subscriptionId): bool
    {
        return in_array($this->getSubscriptionStatus($subscriptionId), ['cancelled', 'canceled']);
    }

    /**
     * Get invoice status
     */
    public function getInvoiceStatus(string $invoiceId): string
    {
        $invoice = $this->getInvoice($invoiceId);
        return $invoice['status'] ?? 'unknown';
    }

    /**
     * Check if invoice is paid
     */
    public function isInvoicePaid(string $invoiceId): bool
    {
        return $this->getInvoiceStatus($invoiceId) === 'paid';
    }

    /**
     * Check if invoice is overdue
     */
    public function isInvoiceOverdue(string $invoiceId): bool
    {
        $invoice = $this->getInvoice($invoiceId);
        
        if ($invoice['status'] === 'open' && isset($invoice['due_date'])) {
            return strtotime($invoice['due_date']) < time();
        }
        
        return false;
    }

    /**
     * Calculate subscription proration
     */
    public function calculateProration(string $subscriptionId, array $changes): array
    {
        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/proration", $changes);
        return $response->json();
    }

    /**
     * Preview subscription changes
     */
    public function previewSubscriptionChanges(string $subscriptionId, array $changes): array
    {
        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/preview", $changes);
        return $response->json();
    }

    /**
     * Add subscription item
     */
    public function addSubscriptionItem(string $subscriptionId, array $itemData): array
    {
        $response = $this->httpClient->post("/billing/subscriptions/{$subscriptionId}/items", $itemData);
        return $response->json();
    }

    /**
     * Update subscription item
     */
    public function updateSubscriptionItem(string $subscriptionId, string $itemId, array $itemData): array
    {
        $response = $this->httpClient->put("/billing/subscriptions/{$subscriptionId}/items/{$itemId}", $itemData);
        return $response->json();
    }

    /**
     * Remove subscription item
     */
    public function removeSubscriptionItem(string $subscriptionId, string $itemId): void
    {
        $this->httpClient->delete("/billing/subscriptions/{$subscriptionId}/items/{$itemId}");
    }

    /**
     * Get billing analytics
     */
    public function getAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/billing/analytics', $params);
        return $response->json();
    }

    /**
     * Export billing data
     */
    public function exportData(array $params = []): array
    {
        $response = $this->httpClient->post('/billing/export', $params);
        return $response->json();
    }
}