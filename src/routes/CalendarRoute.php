<?php

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use BookNail\ResultAPI;
use BookNail\CloseDays;
use BookNail\LockHours;
use BookNail\CalendarService;
use BookNail\BookingService;
use BookNail\LockHoursService;

$app->group('/api/Calendar', function () use ($app) {

    /**
     * GET api/GetDateClosed
     * Summery: Return all closedays list
     * @return CloseDays[]
     */
    $app->get('/GetDateClosed', function (Request $request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(CalendarService::get_date_closed(), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/GetHolidayClosed
     * Summery: Return all closedays and holidays list
     * @return mixed
     */
    $app->get('/GetHolidayClosed', function (Request $request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(CalendarService::get_holiday_and_closed(), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
        /**
     * GET api/RefreshHoliday
     * Summery: Save all new Holiday in DB
     * @return mixed
     */
    $app->get('/RefreshHoliday', function (Request $request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(CalendarService::refresh_holiday(), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/GetLockHoursByDate
     * Summery: Return all closedays and holidays list
     * @return mixed
     */
    $app->get('/GetLockHoursByDate', function (Request $request, Response $response) {
        $LockObj = new LockHours();
        $resultObj = new ResultAPI();
        $date = $request->getParam('Date');
        $endTimeOfLockHours = 0;
        $arrayOfTimesLock = $LockObj->get_slots_lock($date);
        if (count($arrayOfTimesLock) > 0) {
            $count = count($arrayOfTimesLock) - 1;

            $endTimeOfLockHours = $arrayOfTimesLock[$count] + 5;
        }

        $resultObj->set_result($endTimeOfLockHours);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    });

    $app->get('/GetSlotsExist', function (Request $request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(BookingService::get_slots_exists($request->getParam('Date'))['DisableSlots']));
        } catch (Exception $e) {
            return $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode())
                            ->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});

$app->group('/admin/Calendar', function () use ($app) {

    /**
     * POST admin/AddCloseDay
     * Summery: Add close day
     * @return mixed
     */
    $app->post('/AddCloseDay', function (Request $request, Response $response) {
        $CloseDays = new CloseDays();
        $CloseDay = $request->getParsedBody();
        try {
            $CloseDays->Date = $CloseDay["Date"];
            $CloseDays->Notes = $CloseDay["Notes"];
            return $response
                ->withStatus(201) //TODO : need to fix it
                ->withJson(new ResultAPI(CalendarService::add_new_close_day($CloseDays), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * DELTE admin/DeleteCloseDay
     * Summery: Delete close day
     * @return mixed
     */
    $app->Delete('/DeleteCloseDay/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        try {
            return $response
                ->withJson(new ResultAPI(CalendarService::del_close_day($id), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

     /**
     * DELETE admin/DeleteLockHours
     * Summery: Delete lock day
     * @return mixed
     */
    $app->delete('/DeleteLockHours/{id}', function (Request $request, Response $response,array $args) {
        try {
            $resultObj = new ResultAPI(LockHoursService::delete_lock_hours($args['id']), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    
     /**
     * POST admin/AddLockHours
     * Summery: Add lock Hours
     * @return mixed
     */
    $app->post('/AddLockHours', function (Request $request, Response $response) {
        $LockObj = new LockHours();
        $LockObj->from_array($request->getParsedBody());
        try {
            $resultObj = new ResultAPI(LockHoursService::add_new_lock_hours($LockObj), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    

     /**
     * POST admin/GetAllLockHours
     * Summery: Get ALL lock Hours
     * @return mixed
     */
    $app->get('/GetAllLockHours', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(LockHoursService::get_lockHours(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });


});
