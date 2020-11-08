<?php

namespace Giadc\JsonApiResponse\Exceptions;

use Exception;
use Throwable;

class InvalidAttributesRequested extends Exception
{
    public function __construct(array $invalidKeys, array $availableKeys, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Invalid Attribute Requested: %s. Valid keys are %s.',
            implode(', ', $invalidKeys),
            implode(', ', $availableKeys)
        );

        parent::__construct($message, $code, $previous);
    }
}
