<?php

declare(strict_types=1);

namespace Dintero\Contracts;

interface HttpClientInterface
{
    public function get(string $endpoint, array $query = []): HttpResponseInterface;
    public function post(string $endpoint, array $data = []): HttpResponseInterface;
    public function put(string $endpoint, array $data = []): HttpResponseInterface;
    public function patch(string $endpoint, array $data = []): HttpResponseInterface;
    public function delete(string $endpoint): HttpResponseInterface;
}