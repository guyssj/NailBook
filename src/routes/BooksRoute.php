<?php

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

use BookNail\ResultAPI;
use BookNail\Books;
use BookNail\BookingService;
use BookNail\OTPService;

$container = $app->getContainer();
$app->group('/admin/Book', function () use ($app) {
    /**
     * GET admin/GetAllBook2
     * Summery: Return all Books list
     * @return Books[]
     */
    $app->get('/GetAll', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(BookingService::get_books(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * GET admin/GetBooksByCustomer
     * Summery: Return Books of customer
     * @param $CustomerID
     * @return Books[]
     */
    $app->get('/GetBooksByCustomer', function (Request $request, Response $response) {
        $CustomerID = $request->getQueryParams()['CustomerID'];
        try {
            $resultObj = new ResultAPI(BookingService::get_books_by_customerId($CustomerID), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET admin/UpdateBook
     * Summery: Update book to database
     * @param Books
     * @return bool
     */
    $app->put('/UpdateBook', function (Request $request, Response $response) {
        $BooksObj = new Books();
        $books = $request->getParsedBody();
        $BooksObj->from_array($books);
        try {

            $resultObj = new ResultAPI(BookingService::update_book($BooksObj), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * DELETE admin/DeleteBook TODO: change to DELETE
     * Summery: Update book to database
     * @param int BookID
     * @return bool
     */
    $app->Delete('/DeleteBook/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        try {
            $resultObj = new ResultAPI(BookingService::delete_book($id), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * GET admin/GetBookToday
     * Summery: Return number of today books
     * @return int
     */
    $app->get('/GetBookToday', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(BookingService::get_number_books_today(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET admin/GetBookWeek
     * Summery: Return number of week books
     * @return int
     */
    $app->get('/GetBookWeek', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(BookingService::get_number_books_week(), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET admin/GetPriceMonth
     * Summery: Return price for all corrent month
     * @return int
     */
    $app->get('/GetPriceMonth', function (Request $request, Response $response) {
        try {
            return $response->withJson(new ResultAPI(BookingService::get_price_for_book_thismonth(), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET admin/GetPriceByMonth
     * Summery: Return price by month
     * @param $Year,$month
     * @return int
     */
    $app->get('/GetPriceByMonth', function (Request $request, Response $response) {
        $year = $request->getParam('Year');
        $month = $request->getParam('Month');

        try {
            return $response->withJson(new ResultAPI(BookingService::get_price_for_book_month($month, $year), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * PUT admin/AddNote TODO: Change to PUT Method
     * Summery: Return price by month
     * @param $Year,$month
     * @return int
     */
    $app->put('/AddNote', function (Request $request, Response $response) {
        $BooksObj = new Books();
        $books = $request->getParsedBody();
        $BooksObj->BookID = $books['BookID'];
        $BooksObj->Notes = $books['Notes'];
        try {
            return $response->withJson(new ResultAPI(BookingService::add_note($BooksObj), $response->getStatusCode()));
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});


$app->group('/api/Book', function () use ($app) {
    /**
     * GET api/SendRemainder
     * Summery: Send remainder to books tommorow
     * @return <Books></Books>
     */
    $app->get('/SendRemainder', function (Request $request, Response $response) {
        try {
            $resultObj = new ResultAPI(BookingService::send_remainder(), $response->getStatusCode());
            return $response->withJson($resultObj);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * GET api/GetBooksByCustomer
     * Summery: Return Books for customer from context
     * @return <Books></Books>
     */
    $app->get('/GetBooksByCustomer', function (Request $request, Response $response) {
        $token = $request->getHeader('Authorization');
        try {
            $decodeToken = OTPService::verfiy_token($token);
            if ($decodeToken->hasScope(["read"])) {
                $customer = $decodeToken->getAuth();
                $resultObj = new ResultAPI(BookingService::get_books_by_phoneNumber($customer->PhoneNumber), $response->getStatusCode());
                echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception("Token is expired", 403);
            }
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });

    /**
     * GET api/SetBook
     * Summery: Set a book
     * @param Books[]
     * @return bool
     */
    $app->post('/SetBook', function (Request $request, $response) {
        $BooksObj = new Books();
        $books = $request->getParsedBody();
        $BooksObj->from_array($books);
        try {
            $resultObj = new ResultAPI(BookingService::SetBook($BooksObj), $response->getStatusCode());
            $response = $response->withStatus(201);
            return $response->withJson($resultObj);
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
    /**
     * PUT api/UpdateBook
     * Summery: Updated books for customer context
     * @return <Books></Books>
     */
    $app->put('/UpdateBook', function (Request $request, Response $response) {
        $BooksObj = new Books();
        $books = $request->getParsedBody();
        $BooksObj->from_array($books);
        $token = $request->getHeader('Authorization');
        try {
            //$decodeToken = OTPService::verfiy_token($token);
            $decodeToken = $GLOBALS['tokenGlobal'];
            $customer = $decodeToken->getAuth();
            if ($decodeToken->hasScope(["read"]) && $customer->CustomerID == $BooksObj->CustomerID) {
                $resultObj = new ResultAPI(BookingService::update_book($BooksObj), $response->getStatusCode());
                echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
            } elseif ($decodeToken->hasScope(["admin"])) {
                $resultObj = new ResultAPI(BookingService::update_book($BooksObj), $response->getStatusCode());
                echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
            return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
        }
    });
});
