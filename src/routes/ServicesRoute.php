<?php
use \Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * GET Method  /api/GetAllServices
 */
$app->get('/api/GetAllServices', function (Request $request, Response $response) {
    $Services = new Services();
    try {
        $results = $Services->GetAllServices();
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj = new ResultAPI();
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }

});

/**
 * POST api/AddServiceType
 *
 * @param ServiceTypes in  request body
 */
$app->post('/api/AddServiceType', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $ServiceType = new ServiceTypes();
    $ServiceTypeBody = $request->getParsedBody();

    $ServiceType->ServiceTypeName = $ServiceTypeBody['ServiceTypeName'];
    $ServiceType->ServiceID = $ServiceTypeBody['ServiceID'];
    $ServiceType->Price = $ServiceTypeBody['Price'];
    $ServiceType->Duration =$ServiceTypeBody['Duration'];
    $ServiceType->Description =$ServiceTypeBody['Description'];
    $resultObj->set_result($ServiceType->Add());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

/**
 * GET api/GetAllServiceTypeByService?ServiceID={id}
 *
 * Return all Service Types by Service id
 */
$app->get('/api/GetAllServiceTypeByService', function (Request $request, Response $response) {
    $ServiceID = $request->getParam('ServiceID');
    $ServiceTypeObj = new ServiceTypes();

    echo $ServiceTypeObj->GetServiceTypeByID($ServiceID, $response);
});

$app->get('/api/GetAllServiceTypes', function (Request $request, Response $response) {
    $ServiceTypeObj = new ServiceTypes();

    echo $ServiceTypeObj->GetServiceTypes($response);
});