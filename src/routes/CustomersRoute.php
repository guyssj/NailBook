<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;


// $adminMiddleware = function ($request, $response, $next) {
//     $request = $request->withAttribute('role', 'admin');

//     return $next($request, $response);
// };

$app->group('/admin/Customer', function () use ($app) {
    /**
     * GET admin/GetAllCustomers
     * Summery: Return all customers list
     * @return Customer[]
     */
    $app->get('/GetAllCustomers', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(CustomersService::get_customers(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET admin/GetCustomerById
     * Summery: Returns a customer
     * @param int CustomerID
     * @return ResultAPI<Customer>  
     */
    $app->get('/GetCustomerById', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(CustomersService::find_customer_by_id($request->getQueryParams()['CustomerID']), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * GET admin/UpdateCustomer
     * Summery: Update customer property
     * @param int CustomerID
     * @return ResultAPI<Customer>  
     */
    $app->put('/UpdateCustomer', function (Request $request, Response $response) {
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
});

$app->group('/api/Customer', function () use ($app) {
    /**
     * GET api/GenerateToken
     * Summery: Generate a OTP Token and sent to the customer phone number
     * @param $PhoneNumber
     * @return ResultAPI<bool>  
     */
    $app->get('/GenerateToken', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(OTPService::add_otp($request->getQueryParams()['PhoneNumber']), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/VerfiyToken
     * Summery: Returns a customer as user with token (sign in)
     * @return ResultAPI<User>  
     */
    $app->post('/VerfiyToken', function (Request $request, Response $response) {
        $customerAndOTP = $request->getParsedBody();
        try { //TODO : change to OTP class
            $resultObj = new ResultAPI(OTPService::verfiy_otp($customerAndOTP['OTP'], $customerAndOTP['PhoneNumber']), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/GetCustomerByPhone
     * Summery : Return customer by Context scope
     *  @return ResultAPI<Customer>  
     */
    $app->get('/GetCustomerByPhone', function (Request $request, Response $response) {
        $token = $request->getHeader('Authorization');
        $decodeToken = OTPService::verfiy_token($token);
        try {
            if ($decodeToken->hasScope(["read"]))
            {
                $customer = $decodeToken->getAuth();
                $resultObj = new ResultAPI(CustomersService::find_customer_by_phone($customer->PhoneNumber), $response->getStatusCode());
                echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * POST api/AddCustomer
     * Summery : Adds a new customer
     * @param Customer in request body
     */
    $app->post('/AddCustomer', function (Request $request, Response $response) {
        $CustomerObj = new Customer();
        $customer = $request->getParsedBody();
        $CustomerObj->from_array($customer);
        try {
            $resultObj = new ResultAPI(CustomersService::add_customer($CustomerObj), $response->getStatusCode());
            $response = $response->withStatus(201);
            return $response->withJson($resultObj);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});
