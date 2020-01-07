<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

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
 * 
 * get lock hours by date
 */
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

$app->post('/admin/DeleteCloseDay', function (Request $request, Response $response) {
    $CloseDayObj = new CloseDays();
    $resultObj = new ResultAPI();
    $closeDays = $request->getParsedBody();
    $CloseDayObj->CloseDayID = $closeDays['CloseDaysID'];

    $resultObj->set_result($CloseDayObj->del_close_day());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});


$app->post('/admin/DeleteLockHours', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $LockHours = $request->getParsedBody();
    $LockID = $LockHours['idLockHours'];

    $resultObj->set_result(LockHours::delete_lock_hours($LockID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/AddLockHours', function (Request $request, Response $response) {
    $LockObj = new LockHours();
    $resultObj = new ResultAPI();
    $LockHours = $request->getParsedBody();
    $LockObj->StartDate = $LockHours['StartDate'];
    $LockObj->StartAt = $LockHours['StartAt'];
    $LockObj->EndAt = $LockHours['EndAt'];
    $LockObj->Notes = $LockHours['Notes'];


    $resultObj->set_result($LockObj->add_new_lock_hours());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/GetAllLockHours', function (Request $request, Response $response) {
    $LockObj = new LockHours();
    $resultObj = new ResultAPI();

    $resultObj->set_result($LockObj->get_all_lock_hours());
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