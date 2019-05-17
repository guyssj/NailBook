<?php
require '../../vendor/autoload.php';
require 'ResultsApi.class.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Calendar::CALENDAR);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  //$drive = new Google_Service_Calendar($client);
  //$files = $drive->files->listFiles(array())->getItems();
  $service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => true,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

if (empty($events)) {
    print "No upcoming events found.\n";
} else {
    //print "Upcoming events:\n";
    // foreach ($events as $event) {
    //     $start = $event->start->dateTime;
    //     if (empty($start)) {
    //         $start = $event->start->date;
    //     }
    //     printf("%s (%s)\n", $event->getSummary(), $start);
    // }
    $resultObj = new ResultAPI();
    $resultObj->set_result($events);
    echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);

}
} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/NailBook/public/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}