<?php

namespace Giadc\JsonApiResponse\Interfaces;

use Giadc\JsonApiRequest\Requests\RequestParams;

interface AbstractDataModifier
{
    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $data, string $resourceKey, RequestParams $requestParams): array;
}
