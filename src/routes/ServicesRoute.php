<?php
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

    // $ServiceType->ServiceTypeName = $ServiceTypeBody['ServiceTypeName'];
    // $ServiceType->ServiceID = $ServiceTypeBody['ServiceID'];
    // $ServiceType->Price = $ServiceTypeBody['Price'];
    // $ServiceType->Duration = $ServiceTypeBody['Duration'];
    // $ServiceType->Description = $ServiceTypeBody['Description'];
    $ServiceType->from_array($ServiceTypeBody);
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
    $ServiceTypeObj = new ServiceTypes();
    $resultObj = new ResultAPI();
    try {
        $ServiceID = $request->getParam('ServiceID');
        $results = $ServiceTypeObj->GetServiceTypeByID($ServiceID);
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $resultObj->set_result(null);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($e->getMessage());
        return $response->withJson($resultObj);
    }
});

$app->get('/api/GetAllServiceTypes', function (Request $request, Response $response) {
    $ServiceTypeObj = new ServiceTypes();
    $resultObj = new ResultAPI();

    try{
        $results = $ServiceTypeObj->GetServiceTypes();
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        return $response->withJson($resultObj);
        }
    catch (Exception $e) {
        //$resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($e->getMessage());
        return $response->withJson($resultObj);
    }
//    echo $ServiceTypeObj->GetServiceTypes($response);
});
