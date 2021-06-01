<?php

use BookNail\ResultAPI;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

class ExceptionHandler
{
    public function __invoke(Request $request, Response $response, $exception)
    {
        $response->getBody()->rewind();
        $response = $response->withStatus($exception->getCode() <= 0 ? 500 : $exception->getCode());
        return $response
            ->withHeader("Content-Type", "application/json")
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withJson(new ResultAPI(null, $response->getStatusCode(), $exception->getMessage()));
    }
}
