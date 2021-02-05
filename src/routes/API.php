<?php

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use BookNail\TimeSlots;
use BookNail\ResultAPI;
use BookNail\SyncGoogle;


/**
 * GET /api/GetTimeSlots
 *
 * Get all time in min
 *
 * From 8:00 - 18:00
 */
$app->get('/api/GetTimeSlots', function (Request $request, Response $response) {
    $AppointmentDate = $request->getParam('Date');
    if ($AppointmentDate == null) {
        $AppointmentDate = date("Y-m-d"); //date("Y-m-d"); echo "<br>"; //assign selected date by user
    }
    $duration = $request->getParam('Duration');
    if ($duration == null)
        $duration = 0;
    $TimeSlots = TimeSlots::RenderSlots($AppointmentDate,$duration);

    unset($DisableSlotsTimes);
    if (count($TimeSlots) > 0) {
        $TimeSlots = my_array_unique($TimeSlots);
        sort($TimeSlots);
    }


    echo json_encode($TimeSlots, JSON_UNESCAPED_UNICODE);
});

$app->get('/api/GetTimeSlotsForLock', function (Request $request, Response $response) {

    //     check the working hours in database
    //     get day of the week for the date choosed
    $AppointmentDate = $request->getParam('Date');
    if ($AppointmentDate == null) {
        $AppointmentDate = date("Y-m-d"); //date("Y-m-d"); echo "<br>"; //assign selected date by user
    }

    $TimeSlots = TimeSlots::RenderSlotsLock($AppointmentDate);
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
