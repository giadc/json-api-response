<?php

namespace Giadc\JsonApiResponse\Fractal;

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Interfaces\AbstractDataModifier;

class FieldsModifier implements AbstractDataModifier
{
    use ValidateAttributesTrait;

    public function __invoke(array $data, string $resourceKey, RequestParams $requestParams): array
    {
        $fields = $requestParams->getFields()->get($resourceKey);

        if ($fields === null) {
            return $data;
        }

        $this->validateRequestedAttributes($fields, $data);

        return array_filter($data, function ($key) use ($fields) {
            return in_array($key, array_merge($fields, ['id']));
        }, ARRAY_FILTER_USE_KEY);
    }
}
