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
    try {
        $resultObj = new ResultAPI(CustomersService::find_customer_by_id($request->getQueryParams()['CustomerID']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

/**
 * GET api/GetCustomerByPhone
 * 
 * get Customer by Phone number
 */
$app->get('/api/GetCustomerByPhone', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(CustomersService::find_customer_id_by_phone($request->getQueryParams()['PhoneNumber']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

/**
 * POST api/AddCustomer
 *
 * @param Customer in  request body
 */
$app->post('/api/AddCustomer', function (Request $request, Response $response) {

    $CustomerObj = new Customer();
    $customer = $request->getParsedBody();
    $CustomerObj->from_array($customer);
    try {
        $resultObj = new ResultAPI(CustomersService::add_customer($CustomerObj), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->put('/admin/UpdateCustomer', function (Request $request, Response $response) {
    $CustomerObj = new Customer();
    $customer = $request->getParsedBody();
    $CustomerObj->from_array($customer);
    try {
        $resultObj = new ResultAPI(CustomersService::update_customer($CustomerObj), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(false, $response->getStatusCode(), $e->getMessage()));
    }
});

/**
 * 
 * get all customers from DB
 * only admin can get
 * 
 */
$app->get('/admin/GetAllCustomers', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(CustomersService::get_customers(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});
