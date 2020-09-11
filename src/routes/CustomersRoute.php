<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;


/**
 * GET api/GetCustomerById
 * 
 * Get Customer By ID needed OTP
 */
$app->get('/admin/GetCustomerById', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(CustomersService::find_customer_by_id($request->getQueryParams()['CustomerID']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/api/GenerateToken', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(OTPService::add_otp($request->getQueryParams()['PhoneNumber']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/api/VerfiyToken', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(OTPService::verfiy_otp($request->getQueryParams()['OTP'], $request->getQueryParams()['PhoneNumber']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

/**
 * GET api/GetCustomerByPhone
 * 
 * get Customer by Phone number needed JWT TOKEN
 */
$app->get('/api/GetCustomerByPhone', function (Request $request, Response $response) {
    $token = $request->getHeader('Authorization');
    try {
        $phoneNumber = OTPService::verfiy_token($token);
        if ($phoneNumber) {
            $resultObj = new ResultAPI(CustomersService::find_customer_by_phone($phoneNumber), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        }
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
