<?php


use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use BookNail\ResultAPI;
use BookNail\WorkingHours;
use BookNail\WorkingHoursService;

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
        $results = WorkingHoursService::get_hours_by_day($dayOfWeek);
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
 * GET method admin/GetAllWorkingHours
 * Get from DB all working houres
 */

$app->get('/admin/GetAllWorkingHours', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $resultObj->set_result(WorkingHoursService::get_all_hours());
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

$app->post('/admin/AddWorkDay', function (Request $request, Response $response) {
    $WorkingHours = new WorkingHours();
    $resultObj = new ResultAPI();
    $work = $request->getParsedBody();
    $WorkingHours->dayOfWeek = $work['DayOfWeek'];
    $WorkingHours->openTime = $work['OpenTime'];
    $WorkingHours->closeTime = $work['CloseTime'];

    $resultObj->set_result($WorkingHours->set_workingHours());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});