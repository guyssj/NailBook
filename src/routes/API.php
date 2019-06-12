<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Twilio\Rest\Client;
use \Firebase\JWT\JWT;
require '../src/config/ResultsApi.class.php';
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Content-type', 'application/json')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('access-control-expose-headers','X-Token')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            
});
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/admin", /* or ["/api", "/admin"] */
    "attribute" => "decoded_token_data",
    "header" => "X-Token",
    "regexp" => "/(.*)/",
    "cookie" => "userToken",
    "secret" => "supersecretkeyyoushouldnotcommittogithub",
    "algorithm" => ["HS256"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

//Get All Books

$app->get('/api/GetAllBook',function(Request $request , Response $response, $args){
    //Authreazied To API with Key and UserName
    $resultObj = new ResultAPI();
    $key = $request->getParam('key');
    $userName = $request->getParam('userName');
    $auth = checkAPIKey($key,$userName);
    if (!$auth){
        $resultObj->set_ErrorMessage("you are not authorized to access this API"); 
        $resultObj->set_statusCode(403);
       //echo json_encode(array("message" => "you are not authorized to access this API"));
       echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);
       return $response->withStatus(403);
       
    }
    else{
        $sql = "call BookGetAll();";
            //$sql = "SELECT * FROM Customers;";
        try{
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            $resultObj->set_result($row);
            $resultObj->set_statusCode($response->getStatusCode());
            echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);
        }catch(PDOException $e){
            $resultObj->set_ErrorMessage($e->getMessage());
            echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);
        }
    }
});

$app->post('/login',function(Request $request , Response $response){
    //test for git
    $resultObj = new ResultAPI();
    $input = $request->getParsedBody();
    $user = new Users();
    $user->userName =$input['userName'];
    $user->key =$input['key'];
    $auth = $user->checkAPIKey();
    if(!$auth){
        $resultObj->set_result($user->key);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage("These credentials do not match our records.");
        return $this->response->withJson($resultObj);  
    }
    session_start();
    $token = JWT::encode(['key' => $input['key'], 'userName' => $input['userName']], 'supersecretkeyyoushouldnotcommittogithub', "HS256");

    $cookie_name = "TokenApi";
    $cookie_value = $token;
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
    $_SESSION['TokenApi'] = $token;
    
    return $this->response->withHeader('X-Token',$token);

});

$app->get('/admin/GetAllBook2',function(Request $request , Response $response){
    //test for git
    $BooksObj = new Books();
    echo $BooksObj->GetBooks($response);
});

$app->get('/admin/GetCustomerById',function(Request $request , Response $response){
    $Customers = new Customer();
    $resultObj = new ResultAPI();
    $Customers->CustomerID = $request->getParam('CustomerID');
    $resultObj->set_result($Customers->GetCustomerById($Customers->CustomerID));
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);
    
});
$app->get('/api/GetAllServices',function(Request $request , Response $response){
    $sql = "call ServiceGetAll();";
    try{
       $mysqli = new db();
       $mysqli = $mysqli->connect();
       $mysqli->query("set character_set_client='utf8'");
       $mysqli->query("set character_set_results='utf8'");
       $result = $mysqli->query($sql);
       $row = cast_query_results($result);
    //    $jsonobj = ['results' => $row];
    //    $objects = (object)$jsonobj;
       echo json_encode($row,JSON_UNESCAPED_UNICODE);
    //    echo json_encode($objects,JSON_UNESCAPED_UNICODE);
    }catch(PDOException $e){
       echo '{"error":{"text": '.$e->getMessage().'}}';

    }
});


//getDate + 31 days from today
$app->get('/api/GetAllDates',function(Request $request , Response $response){
    $date=date_create(); 
    $id = 0;
    date_add($date, date_interval_create_from_date_string("31 days")); 
      
    $newDate = date_format($date, "d-m-Y"); 
    $period = new DatePeriod(
        new DateTime(''),
        new DateInterval('P1D'),
        new DateTime($newDate)
    );

    foreach( $period as $date1) {
        $id = $id+1;
        $array[] = ['id'=>$id,'date' => $date1->format('d-m-Y')]; 
    }
       echo json_encode($array,JSON_UNESCAPED_UNICODE);
    //    echo json_encode($objects,JSON_UNESCAPED_UNICODE);
   
});

//ggetTimeSlost
$app->get('/api/GetTimeSlots',function(Request $request , Response $response){

    try{
        for ($i=480; $i <= 1080 ; $i=$i+10) { 
            $array[] = ['id'=>$i,'timeSlot' => convertToHoursMins($i, '%02d:%02d')]; 
        }
        echo json_encode($array,JSON_UNESCAPED_UNICODE);
    }
    catch (Exception $e){
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    //    echo json_encode($objects,JSON_UNESCAPED_UNICODE);
   
});

//convert minuts to hours with format
function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}


//Add Book to database
$app->post('/api/SetBook',function(Request $request , Response $response){
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

    if($resultObj->get_result() == -1){
        $resultObj->set_ErrorMessage("Treatment is exists in this time");
    }
    echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);

    
});

$app->get('/api/GetAllServiceTypeByService',function(Request $request , Response $response){
    $ServiceID = $request->getParam('ServiceID');
    $ServiceTypeObj = new ServiceTypes();

    echo $ServiceTypeObj->GetServiceTypeByID($ServiceID,$response);
});



$app->post('/api/AddCustomer',function(Request $request , Response $response){
    $resultObj = new ResultAPI();
    $CustomerObj = new Customer();
    $CustomerObj->FirstName = $request->getParam('FirstName');
    $CustomerObj->LastName = $request->getParam('LastName');
    $CustomerObj->PhoneNumber = $request->getParam('PhoneNumber');
    $resultObj->set_result($CustomerObj->Add());
    $resultObj->set_statusCode($response->getStatusCode());
    echo json_encode($resultObj,JSON_UNESCAPED_UNICODE);
});

//Cast the Fucking Result
function cast_query_results($rs) {
    $fields = mysqli_fetch_fields($rs);
    $data = array();
    $types = array();
    foreach($fields as $field) {
        switch($field->type) {
            case 3:
                $types[$field->name] = 'int';
                break;
            case 4:
                $types[$field->name] = 'float';
                break;
            default:
                $types[$field->name] = 'string';
                break;
        }
    }
    while($row=mysqli_fetch_assoc($rs)) array_push($data,$row);
    for($i=0;$i<count($data);$i++) {
        foreach($types as $name => $type) {
            settype($data[$i][$name], $type);
        }
    }
    return $data;
}