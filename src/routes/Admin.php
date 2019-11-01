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
    $options = ['cost' => 12];
    $user->userName = $input['userName'];
    $user->password = $input['key'];
    $auth = $user->sign_in();

    //if user name or password invalid return code 403 
    if (!$auth) {
        $resultObj->set_result();
        $response = $response->withStatus(403);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage("These credentials do not match our records.");
        return $response->withJson($resultObj);
    }
    session_start();

    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "sub" => $auth,
    ];
    
    $token = JWT::encode($payload, getenv('Secret'), "HS256");

    //set a cookie
    $cookie_name = "TokenApi";
    $cookie_value = $token;
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
    $_SESSION['TokenApi'] = $token;

    $user->token = $token;

    //return $response = $response->withHeader();
    $resultObj->set_result($user);

    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->withHeader('X-Token', $token)
    ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

/**
 * GET admin/GetAllBook2
 *
 * Get all books return in json
 */
$app->get('/admin/GetAllBook2', function (Request $request, Response $response) {
    $BooksObj = new Books();
    echo $BooksObj->GetBooks($response);
});

/**
 * GET admin/GetCustomerById?CustomerID={ID}
 *
 * Get Customer by ID
 *
 */
$app->get('/admin/GetCustomerById', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->CustomerID = $request->getParam('CustomerID');
    $resultObj->set_result($Customers->GetCustomerById($Customers->CustomerID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});


/**
 * 
 * 
 * get all customers from DB
 * only admin can get
 * 
 */
$app->get('/admin/GetAllCustomers', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $resultObj->set_result($Customers->GetAllCustomers());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});