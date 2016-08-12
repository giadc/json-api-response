<?php

use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Responses\Response;
use League\Fractal\Manager;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->response = new Response(new Manager(), new RequestParams());
    }

    public function test_it_generates_an_error_response_for_forbidden()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "FORBIDDEN",
                    "status" => 403,
                    "detail" => "Forbidden",
                ],
            ],
        ];

        $response = $this->response->errorForbidden("Forbidden");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_an_internal_error()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "INTERNAL_ERROR",
                    "status" => 500,
                    "detail" => "Internal Error",
                ],
            ],
        ];

        $response = $this->response->errorInternalError("Internal Error");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_not_found()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "NOT_FOUND",
                    "status" => 404,
                    "detail" => "Not Found",
                ],
            ],
        ];

        $response = $this->response->errorNotFound("Not Found");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_unauthrozied()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "UNAUTHORIZED",
                    "status" => 401,
                    "detail" => "Unauthorized",
                ],
            ],
        ];

        $response = $this->response->errorUnauthorized("Unauthorized");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_invalid_credentials()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "INVALID_CREDENTIALS",
                    "status" => 401,
                    "detail" => "Invalid Credentials",
                ],
            ],
        ];

        $response = $this->response->errorInvalidCredentials("Invalid Credentials");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_a_single_validation_error()
    {
        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "VALIDATION_ERROR",
                    "status" => 400,
                    "detail" => "Error Message",
                    "source" => [
                        "parameter" => "field",
                    ],
                ],
            ],
        ];

        $response = $this->response->errorValidation("Error Message", "field");

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_it_generates_an_error_response_for_multiple_validation_errors()
    {
        $input = [
            'field1' => [
                'Error 1',
                'Error 2',
            ],
            'field2' => [
                'Error 3',
            ],
        ];

        $expectedOutput = [
            "errors" => [
                [
                    "code"   => "VALIDATION_ERROR",
                    "status" => 400,
                    "detail" => "Error 1",
                    "source" => [
                        "parameter" => "field1",
                    ],
                ],
                [
                    "code"   => "VALIDATION_ERROR",
                    "status" => 400,
                    "detail" => "Error 2",
                    "source" => [
                        "parameter" => "field1",
                    ],
                ], [
                    "code"   => "VALIDATION_ERROR",
                    "status" => 400,
                    "detail" => "Error 3",
                    "source" => [
                        "parameter" => "field2",
                    ],
                ],
            ],
        ];

        $response = $this->response->errorsValidation($input);

        $this->assertEquals($expectedOutput, json_decode($response->getContent(), true));
        $this->assertEquals(400, $response->getStatusCode());
    }
}
