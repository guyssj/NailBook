<?php
use \Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


/**
 * GET api/GetCustomerById
 * 
 * Get Customer By ID
 */
$app->get('/api/GetCustomerById', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->CustomerID = $request->getParam('CustomerID');
    $resultObj->set_result($Customers->GetCustomerById($Customers->CustomerID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

/**
 * GET api/GetCustomerByPhone
 * 
 * get Customer by Phone number
 */
$app->get('/api/GetCustomerByPhone', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->PhoneNumber = $request->getParam('PhoneNumber');
    $resultObj->set_result($Customers->GetByPhoneNumber($Customers->PhoneNumber));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

/**
 * POST api/AddCustomer
 *
 * @param Customer in  request body
 */
$app->post('/api/AddCustomer', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $CustomerObj = new Customer();
    $CustomerObj->FirstName = $request->getParam('FirstName');
    $CustomerObj->LastName = $request->getParam('LastName');
    $CustomerObj->PhoneNumber = $request->getParam('PhoneNumber');
    $resultObj->set_result($CustomerObj->Add());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});