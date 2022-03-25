<?php

namespace Giadc\JsonApiResponse\Interfaces;

use Giadc\JsonApiRequest\Requests\RequestParams;

interface AbstractDataModifier
{
    /**
     * @phpstan-param array<string, mixed> $data
     * @phpstan-return array<string, mixed>
     */
    public function __invoke(array $data, string $resourceKey, RequestParams $requestParams): array;
}
