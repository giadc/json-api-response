<?php

namespace Giadc\JsonApiResponse\Fractal;

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Interfaces\AbstractDataModifier;
use Giadc\JsonApiResponse\Interfaces\JsonApiResource;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Request;

abstract class ResourceTransformer extends TransformerAbstract
{
    protected RequestParams $requestParams;

    /** @phpstan-var string[] */
    protected array $defaultFields = [];

    /** @phpstan-var array<class-string<AbstractDataModifier>> */
    protected array $dataProcessors = [
        FieldsModifier::class,
        ExcludeModifier::class,
    ];

    public function __construct(Request $request = null)
    {
        $this->requestParams = new RequestParams($request);
    }

    abstract public static function resourceName(): string;

    /**
     * @phpstan-return array<string, mixed>
     */
    public function transform(JsonApiResource $resource): array
    {
        $this->hasValidArgumentType($resource, static::resourceName());

        return $this->processFieldsWithRequest($resource);
    }

    /**
     * Setter for defaultFields.
     *
     * @phpstan-param string[] $defaultFields
     */
    public function setDefaultFields(array $defaultFields): self
    {
        $this->defaultFields = $defaultFields;

        return $this;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    protected function processFieldsWithRequest(JsonApiResource $resource): array
    {
        $data = $resource->jsonSerialize();

        if (
            !empty($this->defaultFields) &&
            $this->requestParams->getFields()->isEmpty()
        ) {
            $this->requestParams->getFields()->add($resource::getResourceKey(), $this->defaultFields);
        }

        foreach ($this->dataProcessors as $processor) {
            $processor = new $processor();

            if (!$processor instanceof AbstractDataModifier) {
                throw new \Exception(
                    '$processor should be an instance of Giadc\JsonApiResponse\Interfaces\AbstractDataModifier'
                );
            }

            $data = $processor(
                $data,
                $resource::getResourceKey(),
                $this->requestParams
            );
        }

        return $data;
    }

    protected function hasValidArgumentType(JsonApiResource $resource, string $resourceName): bool
    {
        if (!$resource instanceof $resourceName) {
            throw new \Exception(
                'Argument $resource should be an instance of ' . static::resourceName()
            );
        }

        return true;
    }
}
