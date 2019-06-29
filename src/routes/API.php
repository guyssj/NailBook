<?php
use \Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
require '../src/config/ResultsApi.class.php';
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('access-control-expose-headers', 'X-Token')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

});
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/admin", /* or ["/api", "/admin"] */
    "attribute" => "decoded_token_data",
    "header" => "X-Token",
    "regexp" => "/(.*)/",
    "cookie" => "userToken",
    "secret" => "supersecretkeyyoushouldnotcommittogithub",
    "algorithm" => ["HS256"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
]));

/**
 * POST api/login
 *
 * login to books admin and return a token
 */
$app->post('/login', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $input = $request->getParsedBody();
    $user = new Users();
    $user->userName = $input['userName'];
    $user->key = $input['key'];
    $auth = $user->checkAPIKey();

    //this case if check if user return from db and return code
    if (!$auth) {
        $resultObj->set_result($user->key);
        $response = $response->withStatus(403);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage("These credentials do not match our records.");
        return $response->withJson($resultObj);
    }
    session_start();
    $token = JWT::encode(['key' => $input['key'], 'userName' => $input['userName']], 'supersecretkeyyoushouldnotcommittogithub', "HS256");

    $cookie_name = "TokenApi";
    $cookie_value = $token;
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
    $_SESSION['TokenApi'] = $token;

    return $response = $response->withHeader('X-Token', $token);

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
 * GET Method  /api/GetAllServices
 */
$app->get('/api/GetAllServices', function (Request $request, Response $response) {
    $Services = new Services();
    try {
        $results = $Services->GetAllServices();
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj =new ResultAPI();
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }

});

$app->get('/api/GetSlotsExist', function (Request $request, Response $response) {
    $SlotsExist = new Books();
    $resultObj = new ResultAPI();
    try {
        $Date = $request->getParam('Date');
        $results = $SlotsExist->GetBooksByDate($Date);
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

/**
 * GET /api/GetTimeSlots
 *
 * Get all time in min
 *
 * From 8:00 - 18:00
 */
$app->get('/api/GetTimeSlots', function (Request $request, Response $response) {

    try {
        $bookExist = new Books();
        $Date = $request->getParam('Date');
        //get from db if time is exists
        $arryOfTimeExsits = $bookExist->GetBooksByDate($Date);

        // check in array from db and remove from all array time slots
        for ($i = 480; $i <= 1080; $i = $i + 10) {
            $foundTime = false;
            for ($l = 0; $l < count($arryOfTimeExsits); $l++) {
                if ($arryOfTimeExsits[$l] == $i) {
                    $foundTime = true;
                }
            }
            //in this case check if slots found and not add to array TimeSlots
            if ($foundTime == true) {
                continue;
            } else {
                $TimeSlots[] = ['id' => $i, 'timeSlot' => convertToHoursMins($i, '%02d:%02d')];
            }
        }
        echo json_encode($TimeSlots, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
});

/**
 * function to convert Time to HoursMin
 *
 * @param $time TIME
 *
 * @param $format Format of time
 */
function convertToHoursMins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

/**
 * POST /api/SetBook
 *
 * Set appoinemnt
 */
$app->post('/api/SetBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $BooksObj->StartDate = $request->getParam('StartDate');
    $BooksObj->StartAt = $request->getParam('StartAt');
    $BooksObj->CustomerID = $request->getParam('CustomerID');
    $BooksObj->ServiceID = $request->getParam('ServiceID');
    $BooksObj->Durtion = $request->getParam('Durtion');
    $BooksObj->ServiceTypeID = $request->getParam('ServiceTypeID');

    $resultObj->set_result($BooksObj->SetBook($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());

    if ($resultObj->get_result() == -1) {
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    }
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

/**
 * GET api/GetAllServiceTypeByService?ServiceID={id}
 *
 * Return all Service Types by Service id
 */
$app->get('/api/GetAllServiceTypeByService', function (Request $request, Response $response) {
    $ServiceID = $request->getParam('ServiceID');
    $ServiceTypeObj = new ServiceTypes();

    echo $ServiceTypeObj->GetServiceTypeByID($ServiceID, $response);
});

/**
 * POST api/AddCustomer
 *
 * @param Customer in  request body
 */
$app->post('/api/AddCustomer', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $CustomerObj = new Customer();
    $CustomerObj->FirstName = $request->getParam('FirstName');
    $CustomerObj->LastName = $request->getParam('LastName');
    $CustomerObj->PhoneNumber = $request->getParam('PhoneNumber');
    $resultObj->set_result($CustomerObj->Add());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

/**
 * Casting result with current type and not only string
 */
function cast_query_results($rs)
{
    $fields = mysqli_fetch_fields($rs);
    $data = array();
    $types = array();
    foreach ($fields as $field) {
        switch ($field->type) {
            case 3:
                $types[$field->name] = 'int';
                break;
            case 4:
                $types[$field->name] = 'float';
                break;
            default:
                $types[$field->name] = 'string';
                break;
        }
    }
    while ($row = mysqli_fetch_assoc($rs)) {
        array_push($data, $row);
    }

    for ($i = 0; $i < count($data); $i++) {
        foreach ($types as $name => $type) {
            settype($data[$i][$name], $type);
        }
    }
    return $data;
}
