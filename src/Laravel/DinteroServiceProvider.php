<?php

declare(strict_types=1);

namespace Dintero\Laravel;

use Dintero\DinteroClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider for Dintero
 */
class DinteroServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/dintero.php',
            'dintero'
        );

        $this->app->singleton(DinteroClient::class, function ($app) {
            $config = $app['config']['dintero'];
            return new DinteroClient($config);
        });

        $this->app->alias(DinteroClient::class, 'dintero');
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/dintero.php' => config_path('dintero.php'),
            ], 'dintero-config');

            $this->publishes([
                __DIR__ . '/../../routes/webhooks.php' => base_path('routes/dintero-webhooks.php'),
            ], 'dintero-routes');
        }

        if (file_exists(__DIR__ . '/../../routes/webhooks.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/webhooks.php');
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [DinteroClient::class, 'dintero'];
    }
}