<?php

class SyncGoogle
{

    public function __construct()
    {

    }

    public function AddEvent($books,$client)
    {

            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event();
            $reminder = new Google_Service_Calendar_EventReminders();
            $endTimeEvent = new Google_Service_Calendar_EventDateTime();
            $stratTimeEvent = new Google_Service_Calendar_EventDateTime();
            $calendarId = 'primary';
            /* this for calucate the date with time */
            $startTime = new DateTime($books['StartDate']);

            $minutesToAdd = $books['StartAt'];
            $duration = $books['Durtion'];
            $startTime->modify("+{$minutesToAdd} minutes");
            $endTime = new DateTime($startTime->date);

            $endTime->modify("+{$duration} minutes");

            $endTime = $endTime->format('c');

            $startTime = $startTime->format('c');

            //get all details from books
            //$Customer = Customer::GetCustomerById($books['CustomerID']);
            //$ServiceType = ServiceTypes::GetServiceTypeByID2($books['ServiceID']);
            //build the Event class with all propirties
            $reminder->useDefault = true;
            $stratTimeEvent->setDateTime($startTime);
            $stratTimeEvent->setTimeZone('Asia/Jerusalem');
            $endTimeEvent->setDateTime($endTime);
            $endTimeEvent->setTimeZone('Asia/Jerusalem');
            $event->setSummary($books);
            $event->setStart($stratTimeEvent);
            $event->setEnd($endTimeEvent);
            $event->setDescription("תיאור לפגישה תיאור לפגישה");
            $event->setReminders($reminder);

            $event = $service->events->insert($calendarId, $event);

            $resultObj = new ResultAPI();
            $resultObj->set_result($event);
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

    }
}
