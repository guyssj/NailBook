<?php

use BookNail\ResultAPI;
use BookNail\Users;
use BookNail\UsersService;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$app->group('/admin/Users', function () use ($app) {

        /**
     * PoST admin/AddRegistrationId
     * Summery: Add regstration id from device to user
     * @return bool
     */
    $app->post('/AddRegistrationId', function (Request $request,Response $response) {

        try {
            $user = $this->get("User");
            $user->regId = $request->getParsedBody()["regId"];
            $resultObj = new ResultAPI(UsersService::add_regId($user), $response->getStatusCode()); //TODO MOVE regID to users service
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });


    /**
     * GET AddUserName
     *
     * Add user name
     *
     */
    $app->post('/Adduser', function (Request $request, Response $response) {
        $resultObj = new ResultAPI();
        $input = $request->getParsedBody();
        $user = new Users();

        //user auth with hash password
        $user->userName = $input['userName'];
        $user->password = $input['key'];
        $auth = $user->create_new_user();

        $resultObj->set_result($user);
        return $response->withStatus(201)
            ->withJson($resultObj);
    });
});
