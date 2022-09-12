<?php

namespace Giadc\JsonApiResponse\Exceptions;

use Exception;
use Throwable;

class FractalNullDataException extends Exception
{
    public function __construct(
        string|null $resourceKey = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        $resourceKeyNullOrEmpty = ($resourceKey === null || trim($resourceKey) === '');
        $message = sprintf(
            'Unable to retrieve data from Fractal%s',
            $resourceKeyNullOrEmpty ? '.' : "for `$resourceKey`."
        );

        parent::__construct($message, $code, $previous);
    }
}
