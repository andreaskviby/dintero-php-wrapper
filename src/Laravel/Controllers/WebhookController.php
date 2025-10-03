<?php

declare(strict_types=1);

namespace Dintero\Laravel\Controllers;

use Dintero\DinteroClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Webhook controller for handling Dintero webhooks
 */
class WebhookController extends Controller
{
    private DinteroClient $dintero;

    public function __construct(DinteroClient $dintero)
    {
        $this->dintero = $dintero;
    }

    /**
     * Handle incoming webhook
     */
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Dintero-Signature');
        $secret = config('dintero.webhook_secret');

        if (!$signature) {
            Log::warning('Dintero webhook received without signature');
            return response('Webhook signature missing', 400);
        }

        if (!$secret) {
            Log::error('Dintero webhook secret not configured');
            return response('Webhook secret not configured', 500);
        }

        try {
            $event = $this->dintero->webhooks->handleEvent($payload, $signature, $secret);
            
            Log::info('Dintero webhook processed', ['event' => $event['type'] ?? 'unknown']);

            // Dispatch event if Laravel event system is available
            if (class_exists('Illuminate\Support\Facades\Event')) {
                event("dintero.webhook.{$event['type']}", $event);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Dintero webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response('Webhook processing failed', 400);
        }
    }

    /**
     * Test webhook endpoint
     */
    public function test(Request $request): Response
    {
        Log::info('Dintero webhook test received', $request->all());
        return response('Test successful', 200);
    }
}