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

    const CODE_WRONG_ARGS          = 'WRONG_ARGS';
    const CODE_NOT_FOUND           = 'NOT_FOUND';
    const CODE_INTERNAL_ERROR      = 'INTERNAL_ERROR';
    const CODE_UNAUTHORIZED        = 'UNAUTHORIZED';
    const CODE_FORBIDDEN           = 'FORBIDDEN';
    const CODE_VALIDATION_ERROR    = 'VALIDATION_ERROR';
    const CODE_NOT_SEARCHABLE      = 'NOT_SEARCHABLE';
    const CODE_INVALID_CREDENTAILS = 'INVALID_CREDENTIALS';

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
    public function withError($message, $errorCode)
    {
        if ($this->statusCode === 200) {
            trigger_error('Status set to 200..please try again.');
        }

        return $this->withArray(array(
            'errors' => array(
                array(
                    'code'   => $errorCode,
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
     * Return a new JSON Response message
     *
     * @param $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withMessage($message)
    {
        return $this->withArray(array(
            'data' => array(
                'http_code' => $this->statusCode,
                'message'   => $message,
            ),
        ));
    }

    /**
     * Return a new Delete Successful Response form application
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSuccessful($message = 'Delete Successful')
    {
        return $this->withMessage($message);
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
     * Return a new JSON response forbidden error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Return a new JSON response internal error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->withError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->withError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Return a new JSON response unauthorized
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->withError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Return a new JSON response invalid credentials
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorInvalidCredentials($message = 'Invalid Credentails')
    {
        return $this->setStatusCode(401)->withError($message, self::CODE_INVALID_CREDENTAILS);
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
        $error = [
            'code'   => self::CODE_VALIDATION_ERROR,
            'status' => 400,
            'detail' => $message,
        ];

        if (!is_null($field)) {
            $error['source'] = ['parameter' => $field];
        }

        return $this->setStatusCode(400)->withArray([
            'errors' => [
                $error,
            ],
        ]);
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
                    'code'   => self::CODE_VALIDATION_ERROR,
                    'status' => 400,
                    'detail' => $error,
                    'source' => ['parameter' => $field],
                ];
            }, $errors);
        }, array_keys($messages), $messages);

        $response = array_reduce($errorObjects, function ($carrier, $input) {
            return array_merge($carrier, $input);
        }, []);

        return $this->setStatusCode(400)->withErrors($response);
    }

    /**
     * Return a new JSON response not searchable
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotSearchable($message = 'Not Searchable')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_NOT_SEARCHABLE);
    }
}
