<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * GET admin/GetAllBook2
 *
 * Get all books return in json
 */
$app->get('/admin/GetAllBook2', function (Request $request, Response $response) {
    $BooksObj = new Books();
    echo $BooksObj->GetBooks($response);
});


$app->get('/api/GetBookByCustomer', function (Request $request, Response $response) {
    $Book = new Books();
    $resultObj = new ResultAPI();
    try {
        $CustomerID = $request->getParam('CustomerID');
        $results = $Book->GetBooksByCustomer($CustomerID);
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
//multipale books
$app->get('/api/GetBooksByCustomer', function (Request $request, Response $response) {
    $Book = new Books();
    $resultObj = new ResultAPI();
    try {
        $CustomerID = $request->getParam('CustomerID');
        $results = $Book->GetBookByCustomer($CustomerID);
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

/**
 * POST /api/SetBook
 *
 * Set appoinemnt
 */
$app->post('/api/SetBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $BooksObj->StartDate = $request->getParam('StartDate');
    $BooksObj->StartAt = $request->getParam('StartAt');
    $BooksObj->CustomerID = $request->getParam('CustomerID');
    $BooksObj->ServiceID = $request->getParam('ServiceID');
    $BooksObj->Durtion = $request->getParam('Durtion');
    $BooksObj->ServiceTypeID = $request->getParam('ServiceTypeID');

    $resultObj->set_result($BooksObj->SetBook($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());

    if ($resultObj->get_result() == -1) {
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    } else {
        // if book set send a sms for customer
        // $customer = new Customer();
        // $customer = Customer::GetCustomerById($BooksObj->CustomerID);
        // $globalSMS = new globalSMS();
        // $Date = strtotime($BooksObj->StartDate);
        // $NewDate = date("d/m/Y",$Date);
        // $Time = $BooksObj->StartAt;
        // $newTime = hoursandmins($Time);
        // $message ="שלום {$customer['FirstName']} {$customer['LastName']} ,\nנקבעה לך פגישה אצל מיריתוש\n בתאריך {$NewDate} בשעה {$newTime}\n {$LinkWhatApp} ";

        // $globalSMS->send_sms($customer['PhoneNumber'],$message);
    }
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);

});

$app->put('/api/UpdateBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->from_array($books);
    // $BooksObj->StartDate = $books['StartDate'];
    // $BooksObj->StartAt = $books['StartAt'];
    // $BooksObj->BookID = $books['BookID'];

    $resultObj->set_result($BooksObj->UpdateBook($BooksObj));
    if ($resultObj->get_result() <= 0) {
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    }
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});


$app->post('/admin/DeleteBook', function (Request $request, Response $response) {
    $BooksObj = new Books();
    $resultObj = new ResultAPI();
    $books = $request->getParsedBody();
    $BooksObj->BookID = $books['id'];

    $resultObj->set_result($BooksObj->DeleteBook($BooksObj));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/GetBookToday', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $BooksObj = new Books();
    $resultObj->set_result($BooksObj->get_book_today());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/GetBookWeek', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $BooksObj = new Books();
    $BooksObj->get_price_month();
    $resultObj->set_result($BooksObj->get_week_book());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
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
    $resultObj->set_result($BooksObj->get_price_by_month($month,$year)->PriceForAllMonth);
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