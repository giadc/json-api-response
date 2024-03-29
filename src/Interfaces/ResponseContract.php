<?php

declare(strict_types=1);

namespace Giadc\JsonApiResponse\Interfaces;

use Doctrine\Common\Collections\Collection;
use Giadc\JsonApiResponse\Fractal\ResourceTransformer;
use Giadc\JsonApiResponse\Pagination\PaginatedCollection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @phpstan-type Headers array<string,array<string>>
 */
interface ResponseContract
{
    public function getStatusCode(): int;

    /**
     * Sets status code.
     */
    public function setStatusCode(int $statusCode): self;

    /**
     * Return a new JSON response from the application.
     *
     * @phpstan-param array<mixed> $array
     * @phpstan-param Headers $headers
     */
    public function withArray(array $array, array $headers = []): JsonResponse;

    /*
     * Return a new JSON error response from the application.
     */
    public function withError(string $message): JsonResponse;

    /**
     * Return a new Create Successful Response form application.
     *
     * @phpstan-template Entity of object
     * @phpstan-param array<Entity>|\Doctrine\Common\Collections\Collection<int|string, Entity>|Entity|null $entity
     * @phpstan-param Headers $headers
     */
    public function createSuccessful(
        mixed $entity = null,
        TransformerAbstract $transformer = null,
        string $resourceKey = '',
        array $headers = []
    ): SymfonyResponse;

    /**
     * Return a new JSON response from an item
     *
     * @phpstan-template Entity of object
     * @phpstan-param Entity $item
     * @phpstan-param Headers $headers
     */
    public function withItem(
        object $item,
        TransformerAbstract $transformer,
        string $resourceKey,
        array $headers = []
    ): JsonResponse;

    /**
     * Return a new JSON response from an item.
     *
     * @phpstan-param JsonApiResource $item
     * @phpstan-param Headers $headers
     */
    public function withResourceItem(
        JsonApiResource $item,
        ResourceTransformer $transformer,
        array $headers = []
    ): JsonResponse;

    /**
     * Return a new JSON response from a collection.
     *
     * @phpstan-template Entity of object
     * @phpstan-param array<Entity>|Collection<int|string, Entity> $collection
     * @phpstan-param Headers $headers
     */
    public function withCollection(
        array|Collection $collection,
        TransformerAbstract $transformer,
        string $resourceKey = '',
        array $headers = []
    ): SymfonyResponse;

    /**
     * Return a new JSON response of Errors. StatusCode must be
     * a valid Http Error Code.
     *
     * @phpstan-param array<mixed> $errors
     */
    public function withErrors(array $errors): JsonResponse;

    /**
     * Returns a 204 no content.
     *
     * @phpstan-param Headers $headers
     */
    public function noContent(array $headers = []): JsonResponse;

    /**
     * Return a new JSON response from a paginated collection.
     *
     * @phpstan-template Entity of object
     * @phpstan-param PaginatedCollection<string|int, Entity> $paginatedCollection
     * @phpstan-param Headers $headers
     */
    public function withPaginatedCollection(
        PaginatedCollection $paginatedCollection,
        TransformerAbstract $transformer,
        string $resourceKey = '',
        array $headers = []
    ): JsonResponse;

    /**
     * Return a new JSON response from a HttpException.
     */
    public function withHttpException(
        HttpExceptionInterface $httpException
    ): JsonResponse;

    /*
     * Return a new JSON response forbidden error
     */
    public function errorForbidden(string $message = 'Forbidden'): JsonResponse;

    /*
     * Return a new JSON response internal error
     */
    public function errorInternalError(string $message = 'Internal Error'): JsonResponse;

    /*
     * Return a new JSON response not found
     */
    public function errorNotFound(string $message = 'Not Found'): JsonResponse;

    /*
     * Return a new JSON response unauthorized
     */
    public function errorUnauthorized(string $message = 'Unauthorized'): JsonResponse;

    /*
     * Return a new JSON response Validation error
     */
    public function errorValidation(string $message = 'Validation Error'): JsonResponse;

    /**
     * Returns a new JSON response with multiple validation errors.
     * $errors should be passed as an array whose keys are the names of the inputs and whose
     * values are the errors associated with that input. For example:
     *
     *  [
     *      'name' => ['name is too short', 'name should be capitalized'],
     *      'email' => ['email should be a valid email'],
     *  ]
     *
     *  @phpstan-param array<string, string[]> $messages
     */
    public function errorsValidation(array $messages): JsonResponse;

    /*
     * Return a new JSON response not searchable
     */
    public function errorNotSearchable(string $message = 'Not Searchable'): JsonResponse;
}
