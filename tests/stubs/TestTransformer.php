<?php

namespace App;

use App\TestEntity;
use Giadc\JsonApiResponse\Fractal\ResourceTransformer;

/**
 * Class TestTransformer
 */
class TestTransformer extends ResourceTransformer
{
    /**
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * @var array
     */
    protected $defaultIncludes = [];

    protected array $defaultFields = [];

    public static function resourceName(): string
    {
        return TestEntity::class;
    }
}
