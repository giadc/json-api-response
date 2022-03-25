<?php

namespace Giadc\JsonApiResponse\Interfaces;

interface JsonApiResource extends \JsonSerializable
{
    /**
     * Get fractal resource key. (JsonAPI's `type`).
     */
    public static function getResourceKey(): string;

    public function id(): int|string;
}
