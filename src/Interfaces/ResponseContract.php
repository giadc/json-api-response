<?php

namespace Giadc\JsonApiResponse\Interfaces;

use Giadc\JsonApiResponse\Fractal\ResourceTransformer;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @template TKey
 * @template Entity
 */
interface ResponseContract
{
    /*
     * Returns current statusCode
     */
    public function getStatusCode(): int;

    /**
     * Sets status code.
     *
     * @return self<TKey, Entity>
     */
    public function setStatusCode(int $statusCode);

    /**
     * Return a new JSON response from the application.
     *
     * @param array<mixed> $array
     * @param array<string,array<string>> $headers
     */
    public function withArray(array $array, array $headers = []): JsonResponse;

    /*
     * Return a new JSON error response from the application.
     */
    public function withError(string $message): JsonResponse;

    /**
     * Return a new Create Successful Response form application.
     *
     * @param array<Entity>|\Doctrine\Common\Collections\Collection<TKey, Entity>|Entity|null $entity
     * @param array<string,array<string>> $headers
     */
    public function createSuccessful(
        $entity = null,
        TransformerAbstract $transformer = null,
        string $resourceKey = '',
        array $headers = []
    ): SymfonyResponse;

    /*
     * Return a new JSON response from an item
     *
     * @param mixed $item
     * @param array<string,array<string>> $headers
     */
    public function withItem(
        $item,
        TransformerAbstract $transformer,
        string $resourceKey,
        array $headers = []
    ): JsonResponse;

    /**
     * Return a new JSON response from an item.
     *
     * @param array<string,array<string>> $headers
     */
    public function withResourceItem(
        JsonApiResource $item,
        ResourceTransformer $transformer,
        array $headers = []
    ): JsonResponse;

    /**
     * Return a new JSON response from a collection.
     *
     * @param array<Entity>|\Doctrine\Common\Collections\Collection<TKey, Entity> $collection
     */
    public function withCollection(
        $collection,
        TransformerAbstract $transformer,
        string $resourceKey = ''
    ): SymfonyResponse;

    /**
     * Returns a 204 no content.
     *
     * @param array<string,array<string>> $headers
     */
    public function noContent(array $headers = []): JsonResponse;
    /**
     * Return a new JSON response from a paginated collection.
     *
     * @param Paginator<Entity>|PaginatedCollection<Entity> $paginator
     */
    public function withPagination(
        $paginator,
        TransformerAbstract $transformer,
        string $resourceKey = ''
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
     *  @param array<string, string[]> $messages
     */
    public function errorsValidation(array $messages): JsonResponse;

    /*
     * Return a new JSON response not searchable
     */
    public function errorNotSearchable(string $message = 'Not Searchable'): JsonResponse;
}
