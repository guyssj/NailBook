<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require  "../src/services/servicetypes.service.php";
require  "../src/services/services.service.php";

/**
 * GET Method  /api/GetAllServices
 */
$app->get('/api/GetAllServices', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(ServicesService::get_services(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }

});

/**
 * POST api/AddServiceType
 *
 * @param ServiceTypes in  request body
 */
$app->post('/api/AddServiceType', function (Request $request, Response $response) {
    $ServiceType = new ServiceTypes();
    $ServiceType->from_array($request->getParsedBody());
    try {
        $resultObj = new ResultAPI(ServiceTypesService::add_service_type($ServiceType), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

/**
 * GET api/GetAllServiceTypeByService?ServiceID={id}
 *
 * Return all Service Types by Service id
 */
$app->get('/api/GetAllServiceTypeByService', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(ServiceTypesService::find_service_type_by_service($request->getQueryParams()['ServiceID']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/api/GetAllServiceTypes', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(ServiceTypesService::get_service_types(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});
