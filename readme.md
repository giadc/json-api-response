GIADC JSON Response Package
===========================

`currently: v0.1`

This package makes it easy to return HTTP API responses that fit the [JSON API](http://jsonapi.org/) standard.

## Installation
Via Composer
```php
$ composer require giadc/giadc-json-response
```
#### Laravel
Add the following to the `providers` array of your `config/app.php`
```php
Giadc\JsonApiResponse\ServiceProviders\LaravelDoctrineServiceProvider::class,
```

## Usage
Basic Example:
```php
use Giadc\JsonResponse\Responses\Response;

class YourClass {
    _construct(Response $response) {
        $this->response = $response;
    }

    public function jsonApiFunctions() {
        
        return $this->response->success();
    }
}
```

Available functions:
```php
$response->getStatusCode(): int;
$response->setStatusCode(int $statusCode);
$response->withArray(array $array, array $headers = []): JsonResponse;
$response->withError(string $message): JsonResponse;
$response->createSuccessful( $entity = null, TransformerAbstract $transformer = null, string $resourceKey = '', array $headers = []): SymfonyResponse;
$response->withItem( $item, TransformerAbstract $transformer, string $resourceKey, array $headers = []): JsonResponse;
$response->withResourceItem( JsonApiResource $item, ResourceTransformer $transformer, array $headers = []): JsonResponse;
$response->withCollection( $collection, TransformerAbstract $transformer, string $resourceKey = ''): SymfonyResponse;
$response->noContent(array $headers = []): JsonResponse;
$response->withPaginatedCollection(PaginatedCollection $paginator, TransformerAbstract $transformer, string $resourceKey = ''): JsonResponse;
$response->withHttpException( HttpExceptionInterface $httpException): JsonResponse;
$response->errorForbidden(string $message = 'Forbidden'): JsonResponse;
$response->errorInternalError(string $message = 'Internal Error'): JsonResponse;
$response->errorNotFound(string $message = 'Not Found'): JsonResponse;
$response->errorUnauthorized(string $message = 'Unauthorized'): JsonResponse;
$response->errorValidation(string $message = 'Validation Error'): JsonResponse;
$response->errorsValidation(array $messages): JsonResponse;
$response->errorNotSearchable(string $message = 'Not Searchable'): JsonResponse;
```

## Fractal Transformers
The GIADC JSON Response packages uses `league/fractal` for the `withItem()`, `withCollection()`, & `withPaginatedCollection()` responses. 
See [Fractal's documentation](http://fractal.thephpleague.com/transformers) for more information regarding Transformers.

## ResourceTransformer
ResourceTransformer adds the required `transform` that pulls JsonApiResource's data from the required `jsonSerialize` method. It also allows for automatic management of `excludes` and `fields` request parameters options.

## JsonApiResource
Forces entities to have the following methods:

* getResourceKey: We can now reference this instead of hard coding this value very time.
* id: JsonAPI allows requires an id.
* jsonSerialize: Used in the new ResourceTransformer.

Use the JsonApiResource Item response to automatically pass `$resourceKey` to the transformer.
`$response->withResourceItem($item, $transformer, $headers)`

## Excludes
not supported by JsonAPI

Attributes may be excluded from JsonApiResource's response. A decent use case for this would be removing the config/ui/tracking from Campaigns when it's not needed. This would allow us to remove some redundant routes, providing the FE with more flexibility.

Example:

`/api/export/campaigns?excludes[campaigns]=config,ui`


## Fields (https://jsonapi.org/format/#fetching-sparse-fieldsets)

> An empty value indicates that no fields should be returned.
> ...
> If a client requests a restricted set of fields for a given resource type, an endpoint MUST NOT include additional fields in resource objects of that type in its response.

Example:
`/api/permissionGroups?fields[permissionGroups]=name&include=permissions&fields[permissions]=`

```
{
  "data": [
    {
      "type": "permissionGroups",
      "id": "adops",
      "attributes": { "name": "adops" },
      "relationships": {
        "permissions": {
          "data": [{ "type": "permissions", "id": "campaigns.tracking" }]
        }
      }
    },
  ],
  "included": [
    { "type": "permissions", "id": "campaigns.tracking", "attributes": {} }
  ]
}
```
