<?php
require '../../vendor/autoload.php';
require 'SyncWithGoogle.class.php';

require 'ResultsApi.class.php';

session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Calendar::CALENDAR);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    //$drive = new Google_Service_Calendar($client);
    //$files = $drive->files->listFiles(array())->getItems();
    $service = new Google_Service_Calendar($client);
    var_dump($_POST);
    //need create sign in to google before add the event
// Print the next 10 events on the user's calendar.
    $calendarId = 'primary';
    $optParams = array(
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c'),
    );

    $event2 = new Google_Service_Calendar_Event();
    $reminder = new Google_Service_Calendar_EventReminders();
    $reminder->useDefault = true;
    $stratTime = new Google_Service_Calendar_EventDateTime();
    $stratTime->setDateTime('2019-07-11T21:30:00');
    $stratTime->setTimeZone('Asia/Jerusalem');
    $endTime = new Google_Service_Calendar_EventDateTime();
    $endTime->setDateTime('2019-07-11T21:40:00');
    $endTime->setTimeZone('Asia/Jerusalem');
    $event2->setSummary("טיפול בציפורן ימין");
    $event2->setStart($stratTime);
    $event2->setEnd($endTime);
    $event2->setDescription("תיאור לפגישה תיאור לפגישה");
    $event2->setReminders($reminder);
    //how to insert event to google calendar
    $event = new Google_Service_Calendar_Event(array(
        'summary' => 'בדיקה',
        'location' => '800 Howard St., San Francisco, CA 94103',
        'description' => 'A chance to hear more about Google\'s developer products.',
        'start' => array(
            'dateTime' => '2019-07-07T09:00:00-03:00',
            'timeZone' => 'Asia/Jerusalem',
        ),
        'end' => array(
            'dateTime' => '2019-07-07T10:00:00-03:00',
            'timeZone' => 'Asia/Jerusalem',
        ),
        'reminders' => array(
            'useDefault' => false,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        ),
    ));

    $event = $service->events->insert($calendarId, $event2);
    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();

    if (empty($events)) {
        print "No upcoming events found.\n";
    } else {
        //print "Upcoming events:\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            // printf("%s (%s)\n", $event->getSummary(), $start);
        }
        $resultObj = new ResultAPI();
        $resultObj->set_result($events);
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/NailBook/public/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
