<?php

use Illuminate\Support\Facades\Route;
use Dintero\Laravel\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Dintero Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle incoming webhooks from Dintero.
|
*/

Route::middleware(config('dintero.webhook_middleware', ['api']))
    ->prefix(config('dintero.webhook_route_prefix', 'dintero/webhooks'))
    ->group(function () {
        Route::post('/', [WebhookController::class, 'handle'])->name('dintero.webhooks.handle');
        Route::post('/test', [WebhookController::class, 'test'])->name('dintero.webhooks.test');
    });