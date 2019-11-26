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
    
    $token = JWT::encode($payload, getenv('Secret'), "HS256");

    //set a cookie
    $cookie_name = "TokenApi";
    $cookie_value = $token;
    //setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
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
 * GET AddUserName
 *
 * Add user name
 *
 */
$app->post('/admin/Adduser', function (Request $request, Response $response) {
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
 * GET method admin/GetAllWorkingHours
 * Get from DB all working houres
 */

$app->get('/admin/GetAllWorkingHours', function (Request $request, Response $response) {
    $WorkingHours = new WorkingHours();
    $resultObj = new ResultAPI();
    $resultObj->set_result($WorkingHours->get_all_hours());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});


/**
 * 
 * PUT Method admin/UpdateWork
 * updated the work houres per dayOfWeek
 */
$app->put('/admin/UpdateWork', function (Request $request, Response $response) {
    $WorkingHours = new WorkingHours();
    $resultObj = new ResultAPI();
    $work = $request->getParsedBody();

    $WorkingHours->dayOfWeek = $work['DayOfWeek'];
    $WorkingHours->openTime = $work['OpenTime'];
    $WorkingHours->closeTime = $work['CloseTime'];

    $resultObj->set_result($WorkingHours->update_workingHours());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

/**
 * 
 * test
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

$app->post('/admin/DeleteBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->BookID = $books['id'];

    $resultObj->set_result($BooksObj->DeleteBook($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/AddNote', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->BookID = $books['BookID'];
    $BooksObj->Notes = $books['Notes'];

    $resultObj->set_result($BooksObj->AddNotes($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/UpdateCustomer', function (Request $request, Response $response) {
    $Customer = new Customer();
    $resultObj = new ResultAPI();
    $customer = $request->getParsedBody();
    $Customer->CustomerID = $customer['CustomerID'];
    $Customer->FirstName = $customer['FirstName'];
    $Customer->LastName = $customer['LastName'];
    $Customer->PhoneNumber = $customer['PhoneNumber'];
    $Customer->Color = $customer['Color'];


    $resultObj->set_result($Customer->Update());
    if ($resultObj->get_result() <= 0 ) {
        $resultObj->set_ErrorMessage("Customer not saved");
    }
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/AddCloseDay', function (Request $request, Response $response) {
    $CloseDays = new CloseDays();
    $resultObj = new ResultAPI();
    $CloseDay = $request->getParsedBody();
    try {
        $CloseDays->Date = $CloseDay["Date"];
        $results = $CloseDays->add_new_close_date();
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }

});