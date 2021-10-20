<?php

use BookNail\Logger;
use Firebase\JWT\JWT;

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use BookNail\ResultAPI;
use BookNail\Users;
use BookNail\UsersService;

/**
 * POST /login
 *
 * login to books admin and return a token
 */
$app->post('/login', function (Request $request, Response $response) {
    $input = $request->getParsedBody();
    $user = new Users();
    //user auth with hash password
    $user->userName = $input['userName'];
    $user->password = $input['key'];

    try {
        return $response
                 ->withStatus(200)
                ->withJson( new ResultAPI(UsersService::sign_in($input['userName'],$input['key']),$response->getStatusCode()));

    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});
