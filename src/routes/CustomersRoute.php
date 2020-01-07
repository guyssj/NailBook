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

$app->put('/admin/UpdateCustomer', function (Request $request, Response $response) {
    $Customer = new Customer();
    $resultObj = new ResultAPI();
    $customer = $request->getParsedBody();
    $Customer->CustomerID = $customer['CustomerID'];
    $Customer->FirstName = $customer['FirstName'];
    $Customer->LastName = $customer['LastName'];
    $Customer->PhoneNumber = $customer['PhoneNumber'];
    $Customer->Color = $customer['Color'];
    $Customer->Notes = $customer['Notes'];


    $resultObj->set_result($Customer->Update());
    if ($resultObj->get_result() <= 0 ) {
        $resultObj->set_ErrorMessage("Customer not saved");
    }
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

/**
 * GET admin/GetCustomerById?CustomerID={ID}
 *
 * Get Customer by ID
 *
 */
$app->get('/admin/GetCustomerById', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->CustomerID = $request->getParam('CustomerID');
    $resultObj->set_result($Customers->GetCustomerById($Customers->CustomerID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

/**
 * 
 * test
 * get all customers from DB
 * only admin can get
 * 
 */
$app->get('/admin/GetAllCustomers', function (Request $request, Response $response) {
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $resultObj->set_result($Customers->GetAllCustomers());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});