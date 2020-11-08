<?php

namespace Giadc\JsonApiResponse\Responses;

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Fractal\ResourceTransformer;
use Giadc\JsonApiResponse\Interfaces\JsonApiResource;
use Giadc\JsonApiResponse\Interfaces\ResponseContract;
use Giadc\JsonApiResponse\Pagination\FractalDoctrinePaginatorAdapter as PaginatorAdapter;
use League\Fractal\Manager;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

ini_set('xdebug.max_nesting_level', '200');

/**
 * Class Response.
 *
 * @template Entity
 *
 * @implements ResponseInterface<string|int, Entity>
 */
class Response implements ResponseContract
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var Manager
     */
    public $fractal;

    /**
     * @var RequestParams
     */
    protected $requestParams;


    public function __construct(
        Manager $fractal,
        RequestParams $requestParams
    ) {
        $this->fractal = $fractal;
        $this->requestParams = $requestParams;

        $this->fractal->setSerializer(new JsonApiSerializer());

        if (isset($_GET['include'])) {
            $fractal->parseIncludes($_GET['include']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withArray(array $array, array $headers = []): JsonResponse
    {
        return new JsonResponse($array, $this->statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function createSuccessful(
        $entity = null,
        TransformerAbstract $transformer = null,
        string $resourceKey = '',
        array $headers = []
    ): SymfonyResponse {
        $this->setStatusCode(201);

        if (is_null($entity) || is_null($transformer)) {
            $response = new JsonResponse(null, $this->getStatusCode(), $headers);
            $response->setData(null);

            return $response;
        }

        if (is_array($entity)) {
            /**
             * @var array<Entity>
             */
            $collection = $entity;

            return $this->withCollection(
                $collection,
                $transformer,
                $resourceKey
            );
        }

        return $this->withItem($entity, $transformer, $resourceKey, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function noContent(array $headers = []): JsonResponse
    {
        $response = new JsonResponse(null, 204, $headers);
        $response->setData(null);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function withResourceItem(
        JsonApiResource $item,
        ResourceTransformer $transformer,
        array $headers = []
    ): JsonResponse {
        return $this->withItem(
            $item,
            $transformer,
            $item::getResourceKey(),
            $headers
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withItem(
        $item,
        TransformerAbstract $transformer,
        string $resourceKey,
        array $headers = []
    ): JsonResponse {
        $resource = new Item($item, $transformer, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray(), $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function withCollection(
        $collection,
        TransformerAbstract $transformer,
        string $resourceKey = ''
    ): SymfonyResponse {
        $resource  = new FractalCollection($collection, $transformer, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function withPagination(
        $paginator,
        TransformerAbstract $transformer,
        string $resourceKey = ''
    ): JsonResponse {
        $collection = new FractalCollection(
            $paginator,
            $transformer,
            $resourceKey
        );

        if ($paginator instanceof PaginatorInterface) {
            $collection->setPaginator($paginator);
        } else {
            $collection->setPaginator(
                new PaginatorAdapter($paginator, $this->requestParams)
            );
        }

        $rootScope = $this->fractal->createData($collection);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function withHttpException(
        HttpExceptionInterface $httpException
    ): JsonResponse {
        $response = new JsonResponse([], $httpException->getStatusCode());
        $statusText =
            JsonResponse::$statusTexts[$httpException->getStatusCode()];

        $data = [
            'errors' => [
                [
                    'code' => $this->getErrorCode(
                        $httpException->getStatusCode()
                    ),
                    'status' => $httpException->getStatusCode(),
                    'detail' => $httpException->getMessage() ?: $statusText,
                ],
            ],
        ];

        $response->setData($data);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function withError(string $message): JsonResponse
    {
        $this->confirmErrorStatusCode();

        return $this->withArray(array(
            'errors' => array(
                array(
                    'code'   => $this->getErrorCode($this->statusCode),
                    'status' => $this->statusCode,
                    'detail' => $message,
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function withErrors(array $errors): JsonResponse
    {
        $this->confirmErrorStatusCode();

        return $this->withArray(array(
            'errors' => $errors,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function errorForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->setStatusCode(403)->withError($message);
    }

    /**
     * {@inheritdoc}
     */
    public function errorInternalError(string $message = 'Internal Error'): JsonResponse
    {
        return $this->setStatusCode(500)->withError($message);
    }

    /**
     * {@inheritdoc}
     */
    public function errorNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->setStatusCode(404)->withError($message);
    }

    /**
     * {@inheritdoc}
     */
    public function errorUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->setStatusCode(401)->withError($message);
    }

    /**
     * {@inheritdoc}
     */
    public function errorValidation(
        string $message = 'Validation Error',
        string $field = null
    ): JsonResponse {
        $error = array(
            'code'   => "VALIDATION_ERROR",
            'status' => 422,
            'detail' => $message,
        );

        if (!is_null($field)) {
            $error['source'] = ['parameter' => $field];
        }

        return $this->setStatusCode(422)->withArray(array(
            'errors' => array(
                $error,
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function errorsValidation(array $messages): JsonResponse
    {
        $errorObjects = array_map(function ($field, $errors) {
            return array_map(function ($error) use ($field) {
                return [
                    'code'   => "VALIDATION_ERROR",
                    'status' => 422,
                    'detail' => $error,
                    'source' => ['parameter' => $field],
                ];
            }, $errors);
        }, array_keys($messages), $messages);

        $response = array_reduce($errorObjects, function ($carrier, $input) {
            return array_merge($carrier, $input);
        }, []);

        return $this->setStatusCode(422)->withErrors($response);
    }

    /**
     * {@inheritdoc}
     */
    public function errorNotSearchable(string $message = 'Not Searchable'): JsonResponse
    {
        return $this->setStatusCode(403)->withError($message);
    }

    /**
     * Get the error code for the given status code.
     */
    protected function getErrorCode(int $statusCode): string
    {
        /**
         * @var string
         */
        $statusText = JsonResponse::$statusTexts[$statusCode];

        return strtoupper(str_replace(' ', '_', $statusText));
    }

    /**
     * Confirms appropriate StatusCode for an error.
     */
    protected function confirmErrorStatusCode(): void
    {
        if ($this->statusCode < 400) {
            trigger_error('Status should be greater than 400 for errors!');
        }
    }
}
