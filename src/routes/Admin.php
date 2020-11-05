<?php
use Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * POST /login
 *
 * login to books admin and return a token
 */
$app->post('/login', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $input = $request->getParsedBody();
    $user = new Users();

    $now = new DateTime();
    $future = new DateTime("now +2 hours");

    //user auth with hash password
    $user->userName = $input['userName'];
    $user->password = $input['key'];
    $auth = $user->sign_in();

    //if user name or password invalid return code 403 
    if (!$auth) {
        $resultObj->set_result("User name or password do not match our records");
        $response = $response->withStatus(403);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage("User name or password do not match our records");
        return $response->withJson($resultObj);
    }
    session_start();

    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "sub" => $auth,
    ];
    $token = JWT::encode($payload, $_SERVER['Secret'], "HS384");

    // //set a cookie
    // $cookie_name = "TokenApi";
    // $cookie_value = $token;
    //setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
    $_SESSION['TokenApi'] = $token;

    $user->token = $token;
    $user->password = "";

    //return $response = $response->withHeader();
    $resultObj->set_result($user);

    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post('/admin/AddRegistrationId', function (Request $request, Response $response) {
    try {
        $device = new Devices();
        $device->from_array($request->getParsedBody());
        $resultObj = new ResultAPI(DeviceService::add_regId($device),$response->getStatusCode());
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
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});