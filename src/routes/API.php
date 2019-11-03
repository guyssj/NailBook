<?php
use \Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;



$app->get('/api/GetCustomerById', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->CustomerID = $request->getParam('CustomerID');
    $resultObj->set_result($Customers->GetCustomerById($Customers->CustomerID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

$app->get('/api/GetCustomerByPhone', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->PhoneNumber = $request->getParam('PhoneNumber');
    $resultObj->set_result($Customers->GetByPhoneNumber($Customers->PhoneNumber));
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
        $resultObj = new ResultAPI();
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }

});

$app->get('/api/GetBookByCustomer', function (Request $request, Response $response) {
    $Book = new Books();
    $resultObj = new ResultAPI();
    try {
        $CustomerID = $request->getParam('CustomerID');
        $results = $Book->GetBooksByCustomer($CustomerID);
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
        $TimeSlots = [];
        $Date = $request->getParam('Date');
        //get from db if time is exists
        $arryOfTimeExsits = $bookExist->GetBooksByDate($Date);

        // $timestepmp = strtotime("now");
        // $times = date('H:i:s', $timestepmp);
        // $time = explode(':', $times);
        // $timeNew = ($time[0] * 60) + ($time[1]) + ($time[2] / 60);
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
    else{

        //if book set send a sms for customer
        $customer = new Customer();
        $customer = Customer::GetCustomerById($BooksObj->CustomerID);
        $globalSMS = new globalSMS();
        $Date = strtotime($BooksObj->StartDate);
        $NewDate = date("d/m/Y",$Date);
        $Time = $BooksObj->StartAt;
        $newTime = hoursandmins($Time);
        $message ="שלום {$customer['FirstName']} {$customer['LastName']} ,\nנקבע לך פגישה אצל מיריתוש\n בתאריך {$NewDate} בשעה {$newTime}";

        $globalSMS->send_sms($customer['PhoneNumber'],$message);
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

$app->get('/api/GetAllServiceTypes', function (Request $request, Response $response) {
    $ServiceTypeObj = new ServiceTypes();

    echo $ServiceTypeObj->GetServiceTypes($response);
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
 * POST api/AddServiceType
 *
 * @param ServiceTypes in  request body
 */
$app->post('/api/AddServiceType', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $ServiceType = new ServiceTypes();
    $ServiceTypeBody = $request->getParsedBody();

    $ServiceType->ServiceTypeName = $ServiceTypeBody['ServiceTypeName'];
    $ServiceType->ServiceID = $ServiceTypeBody['ServiceID'];
    $ServiceType->Price = $ServiceTypeBody['Price'];
    $ServiceType->Duration =$ServiceTypeBody['Duration'];
    $ServiceType->Description =$ServiceTypeBody['Description'];
    $resultObj->set_result($ServiceType->Add());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->put('/api/UpdateBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->StartDate = $books['StartDate'];
    $BooksObj->StartAt = $books['StartAt'];
    $BooksObj->BookID = $books['BookID'];

    $resultObj->set_result($BooksObj->UpdateBook($BooksObj));
    if ($resultObj->get_result() <= 0 ) {
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    }
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



/**
 * POST api/GetTimes
 *
 * @param Customer in  request body
 */
$app->post('/api/Gets', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $client = new Google_Client();
    $client->setAuthConfig ('../src/config/GoogleCalendar-c0c22e92b397.json');

    $client->setApplicationName("BookNail");
    $client->addScope(Google_Service_Calendar::CALENDAR);
    //$client->setAccessToken($books['token']);
    session_start();
    $_SESSION['access_token'] = $client->getAccessToken();
    try {
            $books = $request->getParsedBody();
            $startTime = new DateTime($books['StartDate']);
            $minutesToAdd = $books['StartAt'];
            $duration = $books['Durtion'];
            $startTime->modify("+{$minutesToAdd} minutes");

            $endTime = new DateTime($startTime->date);
            $endTime->modify("+{$duration} minutes");

            $GoogleSync = new SyncGoogle();
            //need create sign in to google before add the event
            $GoogleSync->AddEvent($books,$client);
            $resultObj->set_result($startTime);
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($e->getMessage());
    }
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
        foreach ($types as $orgname => $type) {
            settype($data[$i][$orgname], $type);
        }
    }
    return $data;
}

function hoursandmins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}
