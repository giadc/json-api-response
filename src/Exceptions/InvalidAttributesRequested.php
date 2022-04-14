<?php

namespace Giadc\JsonApiResponse\Exceptions;

use Exception;
use Throwable;

class InvalidAttributesRequested extends Exception
{
    /**
     * @phpstan-param string[] $invalidKeys
     * @phpstan-param string[] $availableKeys
     */
    public function __construct(array $invalidKeys, array $availableKeys, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Invalid Attribute Requested: %s. Valid keys are %s.',
            implode(', ', $invalidKeys),
            implode(', ', $availableKeys)
        );

        parent::__construct($message, $code, $previous);
    }
}
