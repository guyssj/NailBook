<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require  "../src/services/servicetypes.service.php";
require  "../src/services/services.service.php";



$app->group('/api/Service', function () use ($app) {
    /**
     * GET api/GetAllServices
     * Summery: Return list of services
     * @return array[Services]
     */
    $app->get('/GetAll', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(ServicesService::get_services(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * GET api/ServiceTypeByService?ServiceID={id}
     * Summery: Return list of service type by service
     * @param $serviceID
     * @return ServiceTypes[]
     */
    $app->get('/ServiceTypeByService', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(ServiceTypesService::find_service_type_by_service($request->getQueryParams()['ServiceID']), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/GetAllServiceTypes
     * Summery: Return list of service type by service
     * @param $serviceID
     * @return ServiceTypes[]
     */
    $app->get('/GetAllServiceTypes', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(ServiceTypesService::get_service_types(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});

$app->group('/admin/Service', function () use ($app) {
    /**
     * POST admin/AddServiceType
     *  Add a service type
     * @param ServiceTypes in  request body
     */
    $app->post('/AddServiceType', function (Request $request, Response $response) {
        $ServiceType = new ServiceTypes();
        $ServiceType->from_array($request->getParsedBody());
        try {
            $resultObj = new ResultAPI(ServiceTypesService::add_service_type($ServiceType), $response->getStatusCode());
            $response = $response->withStatus(201);
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});
