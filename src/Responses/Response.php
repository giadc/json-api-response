<?php
namespace Giadc\JsonApiResponse\Responses;

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Interfaces\ResponseContract;
use Giadc\JsonApiResponse\Pagination\FractalDoctrinePaginatorAdapter as PaginatorAdapter;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

ini_set('xdebug.max_nesting_level', 200);

/**
 * Class Response
 *
 * @package App\Http\Responses
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

    /**
     * @param Manager         $fractal
     * @param ResponseFactory $response
     * @param RequestParams   $requestParams
     */
    public function __construct(Manager $fractal, RequestParams $requestParams)
    {
        $this->fractal       = $fractal;
        $this->requestParams = $requestParams;

        $this->fractal->setSerializer(new JsonApiSerializer());

        if (isset($_GET['include'])) {
            $fractal->parseIncludes($_GET['include']);
        }
    }

    /**
     * Returns current statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets status code
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param array $array
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withArray(array $array, array $headers = array())
    {
        return new JsonResponse($array, $this->statusCode, $headers);
    }

    /**
     * Return a new JSON error response from the application.
     *
     * @param $message
     * @param $errorCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withError($message)
    {
        if ($this->statusCode === 200) {
            trigger_error('Status set to 200..please try again.');
        }

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
     * Returns a new JSON response with multiple errors
     *
     * @param  array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function withErrors($errors)
    {
        if ($this->statusCode === 200) {
            trigger_error('Status set to 200..please try again.');
        }

        return $this->withArray(array(
            'errors' => $errors,
        ));
    }

    /**
     * Return a new Delete Successful Response form application
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSuccessful()
    {
        return $this->success();
    }

    /**
     * Return a new Create Successful Response form application
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSuccessful(array $headers = array())
    {
        $this->setStatusCode(201);

        return new JsonResponse('', $this->statusCode, $headers);
    }

    /**
     * Return a new Update Successful Response form application
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSuccessful(array $headers = array())
    {
        return $this->success($headers);
    }

    /**
     * Returns with null data
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function withNull(array $headers = array())
    {
        $this->setStatusCode(200);
        $json = json_decode('{"data": null}');

        return new JsonResponse($json, $this->statusCode, $headers);
    }

    /**
     * Returns a 204 no content
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(array $headers = array())
    {
        $this->setStatusCode(204);

        return new JsonResponse('', $this->statusCode, $headers);
    }

    /**
     * Return a new JSON response from an item
     *
     * @param $item
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withItem($item, $callback, $resourceKey = '')
    {
        $resource  = new Item($item, $callback, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response from a collection
     *
     * @param $collection
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withCollection($collection, $callback, $resourceKey = '')
    {
        $resource  = new FractalCollection($collection, $callback, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response from a paginated collection
     *
     * @param $paginator
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withPagination($paginator, $callback, $resourceKey = '')
    {
        $resource = new FractalCollection($paginator, $callback, $resourceKey);
        $resource->setPaginator(new PaginatorAdapter($paginator, $this->requestParams));
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response from a HttpException
     *
     * @param  HttpExceptionInterface $httpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withHttpException(HttpExceptionInterface $httpException)
    {
        $response   = new JsonResponse([], $httpException->getStatusCode());
        $statusText = JsonResponse::$statusTexts[$httpException->getStatusCode()];

        $data = array(
            "errors" => array(
                array(
                    'code'   => $this->getErrorCode($httpException->getStatusCode()),
                    'status' => $httpException->getStatusCode(),
                    'detail' => $httpException->getMessage() ?: $statusText,
                ),
            ),
        );

        $response->setData($data);

        return $response;
    }

    /**
     * Return a new JSON response forbidden error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->withError($message);
    }

    /**
     * Return a new JSON response internal error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->withError($message);
    }

    /**
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->withError($message);
    }

    /**
     * Return a new JSON response unauthorized
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->withError($message);
    }

    /**
     * Return a new JSON response Validation error
     *
     * @param string $message
     * @param string $field
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorValidation($message = 'Validation Error', $field = null)
    {
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
     * Returns a new JSON response with multiple validation errors.
     * $errors should be passed as an array whose keys are the names of the inputs and whose
     * values are the errors associated with that input. For example:
     *
     *  [
     *      'name' => ['name is too short', 'name should be capitalized'],
     *      'email' => ['email should be a valid email'],
     *  ]
     *
     * @param  array $messages
     * @return \Illuminate\Http\Resposne
     */
    public function errorsValidation($messages)
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
     * Return a new JSON response not searchable
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotSearchable($message = 'Not Searchable')
    {
        return $this->setStatusCode(403)->withError($message);
    }

    /**
     * Get the error code for the given status code
     *
     * @param  int $statusCode
     * @return string
     */
    private function getErrorCode($statusCode)
    {
        $statusText = JsonResponse::$statusTexts[$statusCode];
        return strtoupper(str_replace(' ', '_', $statusText));
    }
}
