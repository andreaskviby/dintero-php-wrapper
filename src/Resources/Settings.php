<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Settings resource for account settings and configuration management
 */
class Settings extends BaseResource
{
    /**
     * Get all account settings
     */
    public function getAll(): array
    {
        $response = $this->httpClient->get('/settings');
        return $response->json();
    }

    /**
     * Get specific setting
     */
    public function get(string $key): array
    {
        $response = $this->httpClient->get("/settings/{$key}");
        return $response->json();
    }

    /**
     * Update setting
     */
    public function update(string $key, $value): array
    {
        $data = ['value' => $value];
        $response = $this->httpClient->put("/settings/{$key}", $data);
        return $response->json();
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(array $settings): array
    {
        $response = $this->httpClient->put('/settings', ['settings' => $settings]);
        return $response->json();
    }

    /**
     * Reset setting to default
     */
    public function reset(string $key): array
    {
        $response = $this->httpClient->post("/settings/{$key}/reset");
        return $response->json();
    }

    /**
     * Get payment settings
     */
    public function getPaymentSettings(): array
    {
        $response = $this->httpClient->get('/settings/payment');
        return $response->json();
    }

    /**
     * Update payment settings
     */
    public function updatePaymentSettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/payment', $settings);
        return $response->json();
    }

    /**
     * Get notification preferences
     */
    public function getNotificationPreferences(): array
    {
        $response = $this->httpClient->get('/settings/notifications');
        return $response->json();
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(array $preferences): array
    {
        $response = $this->httpClient->put('/settings/notifications', $preferences);
        return $response->json();
    }

    /**
     * Get security settings
     */
    public function getSecuritySettings(): array
    {
        $response = $this->httpClient->get('/settings/security');
        return $response->json();
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/security', $settings);
        return $response->json();
    }

    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(): array
    {
        $response = $this->httpClient->post('/settings/security/two-factor/enable');
        return $response->json();
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(string $code): array
    {
        $data = ['verification_code' => $code];
        $response = $this->httpClient->post('/settings/security/two-factor/disable', $data);
        return $response->json();
    }

    /**
     * Get API rate limits
     */
    public function getRateLimits(): array
    {
        $response = $this->httpClient->get('/settings/rate-limits');
        return $response->json();
    }

    /**
     * Update API rate limits
     */
    public function updateRateLimits(array $limits): array
    {
        $response = $this->httpClient->put('/settings/rate-limits', $limits);
        return $response->json();
    }

    /**
     * Get IP whitelist
     */
    public function getIpWhitelist(): array
    {
        $response = $this->httpClient->get('/settings/security/ip-whitelist');
        return $response->json();
    }

    /**
     * Add IP to whitelist
     */
    public function addIpToWhitelist(string $ip, ?string $description = null): array
    {
        $data = [
            'ip_address' => $ip,
            'description' => $description,
        ];

        $response = $this->httpClient->post('/settings/security/ip-whitelist', $data);
        return $response->json();
    }

    /**
     * Remove IP from whitelist
     */
    public function removeIpFromWhitelist(string $ipId): void
    {
        $this->httpClient->delete("/settings/security/ip-whitelist/{$ipId}");
    }

    /**
     * Get webhook settings
     */
    public function getWebhookSettings(): array
    {
        $response = $this->httpClient->get('/settings/webhooks');
        return $response->json();
    }

    /**
     * Update webhook settings
     */
    public function updateWebhookSettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/webhooks', $settings);
        return $response->json();
    }

    /**
     * Get email templates
     */
    public function getEmailTemplates(): array
    {
        $response = $this->httpClient->get('/settings/email-templates');
        return $response->json();
    }

    /**
     * Update email template
     */
    public function updateEmailTemplate(string $templateId, array $templateData): array
    {
        $response = $this->httpClient->put("/settings/email-templates/{$templateId}", $templateData);
        return $response->json();
    }

    /**
     * Get localization settings
     */
    public function getLocalizationSettings(): array
    {
        $response = $this->httpClient->get('/settings/localization');
        return $response->json();
    }

    /**
     * Update localization settings
     */
    public function updateLocalizationSettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/localization', $settings);
        return $response->json();
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): array
    {
        $response = $this->httpClient->get('/settings/localization/languages');
        return $response->json();
    }

    /**
     * Get supported timezones
     */
    public function getSupportedTimezones(): array
    {
        $response = $this->httpClient->get('/settings/localization/timezones');
        return $response->json();
    }

    /**
     * Get compliance settings
     */
    public function getComplianceSettings(): array
    {
        $response = $this->httpClient->get('/settings/compliance');
        return $response->json();
    }

    /**
     * Update compliance settings
     */
    public function updateComplianceSettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/compliance', $settings);
        return $response->json();
    }

    /**
     * Get data retention settings
     */
    public function getDataRetentionSettings(): array
    {
        $response = $this->httpClient->get('/settings/data-retention');
        return $response->json();
    }

    /**
     * Update data retention settings
     */
    public function updateDataRetentionSettings(array $settings): array
    {
        $response = $this->httpClient->put('/settings/data-retention', $settings);
        return $response->json();
    }

    /**
     * Export settings
     */
    public function export(array $categories = []): array
    {
        $params = empty($categories) ? [] : ['categories' => $categories];
        $response = $this->httpClient->post('/settings/export', $params);
        return $response->json();
    }

    /**
     * Import settings
     */
    public function import(array $settings, bool $overwrite = false): array
    {
        $data = [
            'settings' => $settings,
            'overwrite_existing' => $overwrite,
        ];

        $response = $this->httpClient->post('/settings/import', $data);
        return $response->json();
    }

    /**
     * Get settings history
     */
    public function getHistory(array $params = []): array
    {
        $response = $this->httpClient->get('/settings/history', $params);
        return $response->json();
    }

    /**
     * Revert to previous settings
     */
    public function revertToHistory(string $historyId): array
    {
        $data = ['history_id' => $historyId];
        $response = $this->httpClient->post('/settings/revert', $data);
        return $response->json();
    }

    /**
     * Validate settings
     */
    public function validate(array $settings): array
    {
        $response = $this->httpClient->post('/settings/validate', ['settings' => $settings]);
        return $response->json();
    }

    /**
     * Get default settings
     */
    public function getDefaults(): array
    {
        $response = $this->httpClient->get('/settings/defaults');
        return $response->json();
    }

    /**
     * Reset all settings to defaults
     */
    public function resetToDefaults(array $categories = []): array
    {
        $params = empty($categories) ? [] : ['categories' => $categories];
        $response = $this->httpClient->post('/settings/reset-all', $params);
        return $response->json();
    }
}