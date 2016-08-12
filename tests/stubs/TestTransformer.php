<?php
namespace App;

use App\TestEntity;
use League\Fractal\TransformerAbstract;

/**
 * Class TestTransformer
 */
class TestTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * @param App\TestEntity $entity
     * @return array
     */
    public function transform(TestEntity $entity)
    {
        return [
            'id'   => $entity->getId(),
            'name' => $entity->getName(),
        ];
    }
}
