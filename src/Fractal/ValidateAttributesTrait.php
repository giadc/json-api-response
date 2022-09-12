<?php

namespace Giadc\JsonApiResponse\Fractal;

use Giadc\JsonApiResponse\Exceptions\InvalidAttributesRequested;

trait ValidateAttributesTrait
{

    /**
     * Validates that all requested attributes exist in the data.
     *
     * @phpstan-param string[] $fields
     * @phpstan-param string[] $data
     *
     * @throws InvalidAttributesRequested
     */
    private function validateRequestedAttributes(array $fields, array $data): bool
    {
        $invalidFields = array_diff($fields, array_keys($data));

        if (!empty($invalidFields)) {
            throw new InvalidAttributesRequested($invalidFields, array_keys($data));
        }

        return true;
    }
}
