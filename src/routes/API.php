<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/GetBookByCustomer', function (Request $request, Response $response) {
    $Book = new Books();
    $resultObj = new ResultAPI();
    try {
        $CustomerID = $request->getParam('CustomerID');
        $results = $Book->GetBooksByCustomer($CustomerID);
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $resultObj->set_result(null);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($e->getMessage());
        return $response->withJson($resultObj);
    }

});
//multipale books
$app->get('/api/GetBooksByCustomer', function (Request $request, Response $response) {
    $Book = new Books();
    $resultObj = new ResultAPI();
    try {
        $CustomerID = $request->getParam('CustomerID');
        $results = $Book->GetBookByCustomer($CustomerID);
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $resultObj->set_result(null);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($e->getMessage());
        return $response->withJson($resultObj);
    }

});

$app->get('/api/GetSlotsExist', function (Request $request, Response $response) {
    $SlotsExist = new Books();
    $resultObj = new ResultAPI();
    try {
        $Date = $request->getParam('Date');
        $results = $SlotsExist->GetSlotsExist($Date)['DisableSlots'];
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

$app->get('/api/GetDateClosed', function (Request $request, Response $response) {
    $CloseDays = new CloseDays();
    $resultObj = new ResultAPI();
    try {
        $results = $CloseDays->get_date_closed();
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
 * GET api/GetWorkHoursByDay?dayOfWeek={dayOfWeek}
 *
 * Get Working Hours by day of the week
 *
 * @param DayOfWeek
 */
$app->get('/api/GetWorkHoursByDay', function (Request $request, Response $response) {
    $WorkDay = new WorkingHours();
    $resultObj = new ResultAPI();
    try {
        $dayOfWeek = $request->getParam('dayOfWeek');
        $results = $WorkDay->get_hours_by_day($dayOfWeek);
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

$app->get('/api/GetLockHoursByDate', function (Request $request, Response $response) {
    $LockObj = new LockHours();
    $resultObj = new ResultAPI();
    $date = $request->getParam('Date');
    $endTimeOfLockHours = 0;
    $arrayOfTimesLock = $LockObj->get_slots_lock($date);
    if (count($arrayOfTimesLock) > 0) {
        $count = count($arrayOfTimesLock)-1;

        $endTimeOfLockHours = $arrayOfTimesLock[$count]+5;
    }

    $resultObj->set_result($endTimeOfLockHours);
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

/**
 * GET /api/GetTimeSlots
 *
 * Get all time in min
 *
 * From 8:00 - 18:00
 */
$app->get('/api/GetTimeSlots', function (Request $request, Response $response) {

    //     check the working hours in database
    //     get day of the week for the date choosed
    $AppointmentDate = $request->getParam('Date');
    if ($AppointmentDate == null) {
        $AppointmentDate = date("Y-m-d"); //date("Y-m-d"); echo "<br>"; //assign selected date by user
    }

    $TimeSlots = TimeSlots::RenderSlots($AppointmentDate);
    unset($DisableSlotsTimes);
    if (count($TimeSlots) > 0) {
        $TimeSlots = my_array_unique($TimeSlots);
        sort($TimeSlots);
    }
    echo json_encode($TimeSlots, JSON_UNESCAPED_UNICODE);
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

function my_array_unique($array, $keep_key_assoc = false)
{
    $duplicate_keys = array();
    $tmp = array();

    foreach ($array as $key => $val) {
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val)) {
            $val = (array) $val;
        }

        if (!in_array($val, $tmp)) {
            $tmp[] = $val;
        } else {
            $duplicate_keys[] = $key;
        }

    }

    foreach ($duplicate_keys as $key) {
        unset($array[$key]);
    }

    return $keep_key_assoc ? $array : array_values($array);
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
    } else {
        // if book set send a sms for customer
        // $customer = new Customer();
        // $customer = Customer::GetCustomerById($BooksObj->CustomerID);
        // $globalSMS = new globalSMS();
        // $Date = strtotime($BooksObj->StartDate);
        // $NewDate = date("d/m/Y",$Date);
        // $Time = $BooksObj->StartAt;
        // $newTime = hoursandmins($Time);
        // $message ="שלום {$customer['FirstName']} {$customer['LastName']} ,\nנקבעה לך פגישה אצל מיריתוש\n בתאריך {$NewDate} בשעה {$newTime}\n {$LinkWhatApp} ";

        // $globalSMS->send_sms($customer['PhoneNumber'],$message);
    }
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
    if ($resultObj->get_result() <= 0) {
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    }
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
    $client->setAuthConfig('../src/config/GoogleCalendar-c0c22e92b397.json');

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
        $GoogleSync->AddEvent($books, $client);
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

function minutes($time)
{
    $time = explode(':', $time);
    return ($time[0] * 60) + ($time[1]);
}
