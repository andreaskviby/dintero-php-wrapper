<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Profiles resource for merchant profile and checkout configuration
 */
class Profiles extends BaseResource
{
    /**
     * Get merchant profile
     */
    public function get(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}" : '/profiles/current';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update merchant profile
     */
    public function update(array $data, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}" : '/profiles/current';
        $response = $this->httpClient->put($endpoint, $this->prepareData($data));
        return $response->json();
    }

    /**
     * List all profiles
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/profiles', $params);
        return $response->json();
    }

    /**
     * Create a new profile
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/profiles', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a profile
     */
    public function delete(string $profileId): void
    {
        $this->httpClient->delete("/profiles/{$profileId}");
    }

    /**
     * Get checkout configuration
     */
    public function getCheckoutConfig(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/checkout" : '/profiles/current/checkout';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update checkout configuration
     */
    public function updateCheckoutConfig(array $config, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/checkout" : '/profiles/current/checkout';
        $response = $this->httpClient->put($endpoint, $this->prepareData($config));
        return $response->json();
    }

    /**
     * Get payment methods configuration
     */
    public function getPaymentMethods(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/payment-methods" : '/profiles/current/payment-methods';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update payment methods configuration
     */
    public function updatePaymentMethods(array $paymentMethods, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/payment-methods" : '/profiles/current/payment-methods';
        $response = $this->httpClient->put($endpoint, $paymentMethods);
        return $response->json();
    }

    /**
     * Enable payment method
     */
    public function enablePaymentMethod(string $paymentMethod, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/payment-methods/{$paymentMethod}/enable" : "/profiles/current/payment-methods/{$paymentMethod}/enable";
        $response = $this->httpClient->post($endpoint);
        return $response->json();
    }

    /**
     * Disable payment method
     */
    public function disablePaymentMethod(string $paymentMethod, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/payment-methods/{$paymentMethod}/disable" : "/profiles/current/payment-methods/{$paymentMethod}/disable";
        $response = $this->httpClient->post($endpoint);
        return $response->json();
    }

    /**
     * Get branding configuration
     */
    public function getBranding(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/branding" : '/profiles/current/branding';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update branding configuration
     */
    public function updateBranding(array $branding, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/branding" : '/profiles/current/branding';
        $response = $this->httpClient->put($endpoint, $branding);
        return $response->json();
    }

    /**
     * Upload logo
     */
    public function uploadLogo(string $logoPath, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/branding/logo" : '/profiles/current/branding/logo';
        
        // This would typically handle file upload
        $data = [
            'logo' => base64_encode(file_get_contents($logoPath)),
            'mime_type' => mime_content_type($logoPath),
        ];

        $response = $this->httpClient->post($endpoint, $data);
        return $response->json();
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/notifications" : '/profiles/current/notifications';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(array $settings, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/notifications" : '/profiles/current/notifications';
        $response = $this->httpClient->put($endpoint, $settings);
        return $response->json();
    }

    /**
     * Get security settings
     */
    public function getSecuritySettings(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/security" : '/profiles/current/security';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(array $settings, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/security" : '/profiles/current/security';
        $response = $this->httpClient->put($endpoint, $settings);
        return $response->json();
    }

    /**
     * Get webhook settings
     */
    public function getWebhookSettings(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/webhooks" : '/profiles/current/webhooks';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update webhook settings
     */
    public function updateWebhookSettings(array $settings, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/webhooks" : '/profiles/current/webhooks';
        $response = $this->httpClient->put($endpoint, $settings);
        return $response->json();
    }

    /**
     * Get API keys
     */
    public function getApiKeys(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/api-keys" : '/profiles/current/api-keys';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Generate new API key
     */
    public function generateApiKey(array $keyData, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/api-keys" : '/profiles/current/api-keys';
        $response = $this->httpClient->post($endpoint, $keyData);
        return $response->json();
    }

    /**
     * Revoke API key
     */
    public function revokeApiKey(string $keyId, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/api-keys/{$keyId}/revoke" : "/profiles/current/api-keys/{$keyId}/revoke";
        $response = $this->httpClient->post($endpoint);
        return $response->json();
    }

    /**
     * Get compliance settings
     */
    public function getComplianceSettings(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/compliance" : '/profiles/current/compliance';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update compliance settings
     */
    public function updateComplianceSettings(array $settings, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/compliance" : '/profiles/current/compliance';
        $response = $this->httpClient->put($endpoint, $settings);
        return $response->json();
    }

    /**
     * Get risk settings
     */
    public function getRiskSettings(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/risk" : '/profiles/current/risk';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Update risk settings
     */
    public function updateRiskSettings(array $settings, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/risk" : '/profiles/current/risk';
        $response = $this->httpClient->put($endpoint, $settings);
        return $response->json();
    }

    /**
     * Test checkout configuration
     */
    public function testCheckoutConfig(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/checkout/test" : '/profiles/current/checkout/test';
        $response = $this->httpClient->post($endpoint);
        return $response->json();
    }

    /**
     * Preview checkout appearance
     */
    public function previewCheckout(array $config, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/checkout/preview" : '/profiles/current/checkout/preview';
        $response = $this->httpClient->post($endpoint, $config);
        return $response->json();
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/currencies" : '/profiles/current/currencies';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Get supported countries
     */
    public function getSupportedCountries(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/countries" : '/profiles/current/countries';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Get profile status
     */
    public function getStatus(?string $profileId = null): string
    {
        $profile = $this->get($profileId);
        return $profile['status'] ?? 'unknown';
    }

    /**
     * Check if profile is active
     */
    public function isActive(?string $profileId = null): bool
    {
        return $this->getStatus($profileId) === 'active';
    }

    /**
     * Check if profile is verified
     */
    public function isVerified(?string $profileId = null): bool
    {
        $profile = $this->get($profileId);
        return $profile['verified'] ?? false;
    }

    /**
     * Get verification status
     */
    public function getVerificationStatus(?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/verification" : '/profiles/current/verification';
        $response = $this->httpClient->get($endpoint);
        return $response->json();
    }

    /**
     * Submit verification documents
     */
    public function submitVerificationDocuments(array $documents, ?string $profileId = null): array
    {
        $endpoint = $profileId ? "/profiles/{$profileId}/verification/documents" : '/profiles/current/verification/documents';
        $response = $this->httpClient->post($endpoint, $documents);
        return $response->json();
    }
}