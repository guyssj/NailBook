<?php

use Slim\Http\Response as Response;
use BookNail\ResultAPI;
use BookNail\HolidayService;

$app->group('/api/Holiday', function () use ($app) {
    $app->get('/GetHolidays', function ($request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(HolidayService::get_holidays(), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});
