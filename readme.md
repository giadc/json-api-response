GIADC JSON Response Package
===========================

`currently: v0.1`

This package makes it easy to return HTTP API responses that fit the [JSON API](http://jsonapi.org/) standard.

## Installation
Via Composer
```
$ composer require giadc/giadc-json-response
```
#### Laravel
Add the following to the `providers` array of your `config/app.php`
```
Giadc\JsonApiResponse\ServiceProviders\LaravelDoctrineServiceProvider::class,
```

## Usage
Basic Example:
```
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
```
$response->getStatusCode();
$response->setStatusCode($statusCode);
$response->withArray(array $array, array $headers = array());
$response->withError($message, $errorCode);
$response->withMessage($message);
$response->deleteSuccessful($message = 'Delete Successful');
$response->withItem($item, $transformer); // See below regarding Transformer
$response->withCollection($collection, $transformer);
$response->withPagination($paginator, $transformer);
$response->errorForbidden($message = 'Forbidden');
$response->errorInternalError($message = 'Internal Error');
$response->errorNotFound($message = 'Not Found');
$response->errorUnauthorized($message = 'Unauthorized');
$response->errorInvalidCredentials($message = 'Invalid Credentails');
$response->errorWrongArgs($message = 'Wrong Arguments');
$response->errorValidation($message = 'Validation Error');
$response->errorNotSearchable($message = 'Not Searchable');
$response->success($headers);
```

## Transformers
The GIADC JSON Response packages uses `league/fractal` for the `withItem()`, `withCollection()`, & `withPagination()` responses. See [Fractal's documentation](http://fractal.thephpleague.com/transformers) for more information regarding Transformers.