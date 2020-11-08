<?php

namespace Giadc\JsonApiResponse\Fractal;

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Interfaces\AbstractDataModifier;

class ExcludeModifier implements AbstractDataModifier
{
    use ValidateAttributesTrait;

    public function __invoke(
        array $data,
        string $resourceKey,
        RequestParams $requestParams
    ): array {
        $excludes = $requestParams->getExcludes()->get($resourceKey);

        // If fields option exists for requested resource..ignore excludes
        if (
            $excludes === null ||
            $requestParams->getFields()->get($resourceKey) !== null
        ) {
            return $data;
        }

        $this->validateRequestedAttributes($excludes, $data);

        return array_filter(
            $data,
            function ($key) use ($excludes) {
                return !in_array($key, $excludes);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
