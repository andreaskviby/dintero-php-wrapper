<?php

declare(strict_types=1);

namespace Dintero\Contracts;

use Dintero\Http\HttpClient;
use Dintero\Support\Configuration;

interface DinteroClientInterface
{
    public function getConfig(): Configuration;
    public function getHttpClient(): HttpClient;
    public function ping(): bool;
    public function getAccount(): array;
}