<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Reports resource for analytics and financial reporting
 */
class Reports extends BaseResource
{
    /**
     * Get transaction reports
     */
    public function getTransactionReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/transactions', $params);
        return $response->json();
    }

    /**
     * Get payout reports
     */
    public function getPayoutReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/payouts', $params);
        return $response->json();
    }

    /**
     * Get reconciliation reports
     */
    public function getReconciliationReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/reconciliation', $params);
        return $response->json();
    }

    /**
     * Get revenue reports
     */
    public function getRevenueReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/revenue', $params);
        return $response->json();
    }

    /**
     * Get customer reports
     */
    public function getCustomerReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/customers', $params);
        return $response->json();
    }

    /**
     * Get subscription reports
     */
    public function getSubscriptionReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/subscriptions', $params);
        return $response->json();
    }

    /**
     * Get refund reports
     */
    public function getRefundReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/refunds', $params);
        return $response->json();
    }

    /**
     * Get fee reports
     */
    public function getFeeReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/fees', $params);
        return $response->json();
    }

    /**
     * Get payment method reports
     */
    public function getPaymentMethodReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/payment-methods', $params);
        return $response->json();
    }

    /**
     * Generate custom report
     */
    public function generateCustomReport(array $reportConfig): array
    {
        $response = $this->httpClient->post('/reports/custom', $reportConfig);
        return $response->json();
    }

    /**
     * Get report by ID
     */
    public function getReport(string $reportId): array
    {
        $response = $this->httpClient->get("/reports/{$reportId}");
        return $response->json();
    }

    /**
     * Download report
     */
    public function downloadReport(string $reportId, string $format = 'csv'): array
    {
        $response = $this->httpClient->get("/reports/{$reportId}/download", ['format' => $format]);
        return $response->json();
    }

    /**
     * Schedule report
     */
    public function scheduleReport(array $scheduleConfig): array
    {
        $response = $this->httpClient->post('/reports/schedule', $scheduleConfig);
        return $response->json();
    }

    /**
     * List scheduled reports
     */
    public function listScheduledReports(): array
    {
        $response = $this->httpClient->get('/reports/scheduled');
        return $response->json();
    }

    /**
     * Update scheduled report
     */
    public function updateScheduledReport(string $scheduleId, array $scheduleConfig): array
    {
        $response = $this->httpClient->put("/reports/scheduled/{$scheduleId}", $scheduleConfig);
        return $response->json();
    }

    /**
     * Delete scheduled report
     */
    public function deleteScheduledReport(string $scheduleId): void
    {
        $this->httpClient->delete("/reports/scheduled/{$scheduleId}");
    }

    /**
     * Get dashboard analytics
     */
    public function getDashboardAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/dashboard', $params);
        return $response->json();
    }

    /**
     * Get KPI metrics
     */
    public function getKpiMetrics(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/kpi', $params);
        return $response->json();
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/performance', $params);
        return $response->json();
    }

    /**
     * Get conversion metrics
     */
    public function getConversionMetrics(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/conversion', $params);
        return $response->json();
    }

    /**
     * Get churn analysis
     */
    public function getChurnAnalysis(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/churn', $params);
        return $response->json();
    }

    /**
     * Get cohort analysis
     */
    public function getCohortAnalysis(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/cohort', $params);
        return $response->json();
    }

    /**
     * Get fraud reports
     */
    public function getFraudReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/fraud', $params);
        return $response->json();
    }

    /**
     * Get risk analysis
     */
    public function getRiskAnalysis(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/risk', $params);
        return $response->json();
    }

    /**
     * Get geographic reports
     */
    public function getGeographicReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/geographic', $params);
        return $response->json();
    }

    /**
     * Get time-series data
     */
    public function getTimeSeriesData(string $metric, array $params = []): array
    {
        $response = $this->httpClient->get("/reports/time-series/{$metric}", $params);
        return $response->json();
    }

    /**
     * Get financial summary
     */
    public function getFinancialSummary(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/financial-summary', $params);
        return $response->json();
    }

    /**
     * Get tax reports
     */
    public function getTaxReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/tax', $params);
        return $response->json();
    }

    /**
     * Export data for accounting
     */
    public function exportForAccounting(array $params = []): array
    {
        $response = $this->httpClient->post('/reports/accounting-export', $params);
        return $response->json();
    }

    /**
     * Get settlement reports
     */
    public function getSettlementReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/settlements', $params);
        return $response->json();
    }

    /**
     * Get dispute reports
     */
    public function getDisputeReports(array $params = []): array
    {
        $response = $this->httpClient->get('/reports/disputes', $params);
        return $response->json();
    }

    /**
     * Create report template
     */
    public function createTemplate(array $templateData): array
    {
        $response = $this->httpClient->post('/reports/templates', $templateData);
        return $response->json();
    }

    /**
     * Get report template
     */
    public function getTemplate(string $templateId): array
    {
        $response = $this->httpClient->get("/reports/templates/{$templateId}");
        return $response->json();
    }

    /**
     * List report templates
     */
    public function listTemplates(): array
    {
        $response = $this->httpClient->get('/reports/templates');
        return $response->json();
    }

    /**
     * Update report template
     */
    public function updateTemplate(string $templateId, array $templateData): array
    {
        $response = $this->httpClient->put("/reports/templates/{$templateId}", $templateData);
        return $response->json();
    }

    /**
     * Delete report template
     */
    public function deleteTemplate(string $templateId): void
    {
        $this->httpClient->delete("/reports/templates/{$templateId}");
    }

    /**
     * Generate report from template
     */
    public function generateFromTemplate(string $templateId, array $params = []): array
    {
        $response = $this->httpClient->post("/reports/templates/{$templateId}/generate", $params);
        return $response->json();
    }

    /**
     * Get report status
     */
    public function getReportStatus(string $reportId): string
    {
        $report = $this->getReport($reportId);
        return $report['status'] ?? 'unknown';
    }

    /**
     * Check if report is ready
     */
    public function isReportReady(string $reportId): bool
    {
        return $this->getReportStatus($reportId) === 'completed';
    }

    /**
     * Get available report formats
     */
    public function getAvailableFormats(): array
    {
        return ['csv', 'xlsx', 'pdf', 'json'];
    }

    /**
     * Get supported date ranges
     */
    public function getSupportedDateRanges(): array
    {
        return [
            'today',
            'yesterday',
            'last_7_days',
            'last_30_days',
            'last_90_days',
            'this_month',
            'last_month',
            'this_quarter',
            'last_quarter',
            'this_year',
            'last_year',
            'custom',
        ];
    }

    /**
     * Get report metrics
     */
    public function getReportMetrics(string $reportType): array
    {
        $response = $this->httpClient->get("/reports/{$reportType}/metrics");
        return $response->json();
    }

    /**
     * Get data export status
     */
    public function getExportStatus(string $exportId): array
    {
        $response = $this->httpClient->get("/reports/exports/{$exportId}/status");
        return $response->json();
    }

    /**
     * Cancel report generation
     */
    public function cancelReport(string $reportId): array
    {
        $response = $this->httpClient->post("/reports/{$reportId}/cancel");
        return $response->json();
    }
}