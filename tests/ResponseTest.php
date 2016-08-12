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

    public function test_it_generates_a_validation_errors_response()
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
    }
}
