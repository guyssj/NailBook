<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

/**
 * GET admin/GetAllBook2
 *
 * Get all books return in json
 */
$app->get('/admin/GetAllBook2', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(BookingService::get_books(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});


//multipale books
$app->get('/api/GetBooksByCustomer', function (Request $request, Response $response) {
    $token = $request->getHeader('Authorization');
    try {
        $phoneNumber = OTPService::verfiy_token($token);
        if ($phoneNumber) {
            $resultObj = new ResultAPI(BookingService::get_books_by_phoneNumber($phoneNumber), $response->getStatusCode());
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
 * POST /api/SetBook
 *
 * Set appoinemnt
 */
$app->post('/api/SetBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $books = $request->getParsedBody();
    $BooksObj->from_array($books);
    try {
        $resultObj = new ResultAPI(BookingService::SetBook($BooksObj), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->put('/api/UpdateBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $books = $request->getParsedBody();
    $BooksObj->from_array($books);
    $token = $request->getHeader('Authorization');
    try {
        $phoneNumber = OTPService::verfiy_token($token);
        if ($phoneNumber) {
            $resultObj = new ResultAPI(BookingService::update_book($BooksObj), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});
$app->put('/admin/UpdateBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $books = $request->getParsedBody();
    $BooksObj->from_array($books);
    //$token = $request->getHeader('Authorization');
    try {
            $resultObj = new ResultAPI(BookingService::update_book($BooksObj), $response->getStatusCode());
            echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});


$app->post('/admin/DeleteBook', function (Request $request, Response $response) {
    $books = $request->getParsedBody();
    try {
        $resultObj = new ResultAPI(BookingService::delete_book($books['id']), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/admin/GetBookToday', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(BookingService::get_number_books_today(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/admin/GetBookWeek', function (Request $request, Response $response) {
    try {
        $resultObj = new ResultAPI(BookingService::get_number_books_week(), $response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $response = $response->withStatus($e->getCode() <= 0 ? 500 : $e->getCode());
        return $response->withJson(new ResultAPI(null, $response->getStatusCode(), $e->getMessage()));
    }
});

$app->get('/admin/GetPriceMonth', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $BooksObj = new Books();
    $resultObj->set_result($BooksObj->get_price_month()->PriceForAllMonth);
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/GetPriceByMonth', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $BooksObj = new Books();
    $year = $request->getParam('Year');
    $month = $request->getParam('Month');
    $resultObj->set_result($BooksObj->get_price_by_month($month, $year)->PriceForAllMonth);
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/AddNote', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->BookID = $books['BookID'];
    $BooksObj->Notes = $books['Notes'];

    $resultObj->set_result($BooksObj->AddNotes($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});
