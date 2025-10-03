<?php

declare(strict_types=1);

namespace Dintero\Laravel\Facades;

use Dintero\DinteroClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dintero\Resources\PaymentSessions paymentSessions()
 * @method static \Dintero\Resources\Customers customers()
 * @method static \Dintero\Resources\Orders orders()
 * @method static \Dintero\Resources\Refunds refunds()
 * @method static \Dintero\Resources\Webhooks webhooks()
 * @method static \Dintero\Support\Configuration getConfig()
 * @method static \Dintero\Http\HttpClient getHttpClient()
 * @method static bool ping()
 * @method static array getAccount()
 */
class Dintero extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DinteroClient::class;
    }
}