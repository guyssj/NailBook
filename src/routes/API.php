<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Twilio\Rest\Client;
require '../src/config/ResultsApi.class.php';
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-type', 'application/json')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            
});

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

//Get All Books 2 without result obj

$app->get('/api/GetAllBook2',function(Request $request , Response $response){
    $BooksObj = new Books();
    echo $BooksObj->GetBooks($response);
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
    $BooksObj->StartDate = $request->getParam('StartDate');
    $BooksObj->EndDate = $request->getParam('EndDate');
    $BooksObj->CustomerID = $request->getParam('CustomerID');
    $BooksObj->ServiceID = $request->getParam('ServiceID');
    $BooksObj->Durtion = $request->getParam('Durtion');
    $BooksObj->ServiceTypeID = $request->getParam('ServiceTypeID');

    
    echo $BooksObj->SetBook($BooksObj);
});

$app->get('/api/GetAllServiceTypeByService',function(Request $request , Response $response){
    $ServiceID = $request->getParam('ServiceID');
    $sql = "call ServiceTypeByServiceIDGet('$ServiceID');";

    
    try{
        $mysqli = new db();
        $mysqli = $mysqli->connect();
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set character_set_results='utf8'");
        $result = $mysqli->query($sql);
        $row = cast_query_results($result);
        echo json_encode($row,JSON_UNESCAPED_UNICODE);
    }
    catch(PDOException $e){
        $var = (string)$e->getMessage();
        echo '{"error": "'.$var.'"}';
    }
});



$app->post('/api/AddCustomer',function(Request $request , Response $response){
    $StartDate = $request->getParam('StartDate');
    $EndDate = $request->getParam('EndDate');
    $CustomerID = $request->getParam('CustomerID');
    $ServiceID = $request->getParam('ServiceID');
    $Durtion = $request->getParam('Durtion');
    
    
    // $sql = "call CustomerAdd('$First_name','$last_name','$phoneNumber','$phoneNumber2','$datecous','$email','$passport',@id);";
    // $sql = "SELECT phoneNumber FROM Customers WHERE phoneNumber=$phoneNumber LIMIT 1";
    try{
        $mysqli = new db();
        $mysqli = $mysqli->connect();
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set character_set_results='utf8'");
        $result = $mysqli->query($sql);
        $row_cnt = $result->num_rows;
        //echo $result;
        if($row_cnt > 0){
            echo '{"error": "Customer has Exsits in database" }';
            $result->close();
        }
        else{
            $sql2 = "INSERT INTO Customers (first_name,Last_name,phoneNumber,phoneNumber2,datecous,email,passport)
            VALUES (:First_name,:last_name,:phoneNumber,:phoneNumber2,:datecous,:email,:passport)";
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql2);
        
                $smst->bindParam(':First_name', $First_name);
                $smst->bindParam(':last_name', $last_name);
                $smst->bindParam(':phoneNumber', $phoneNumber);
                $smst->bindParam(':phoneNumber2', $phoneNumber2);
                $smst->bindParam(':datecous', $datecous);
                $smst->bindParam(':email', $email);
                $smst->bindParam(':passport', $passport);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
        
                $row = $smst->execute();
        
                echo $row;

                $mysqli->close();
        }
    }catch(PDOException $e){
        $var = (string)$e->getMessage();
        echo '{"error": "'.$var.'"}';
    }

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


function checkAPIKey($key,$userName){
    $sql = "SELECT * FROM APIKeys WHERE APIKey = '$key' AND UserName = '$userName' LIMIT 1";
    $mysqli = new db();
    $mysqli = $mysqli->connect();
    $mysqli->query("set character_set_client='utf8'");
    $mysqli->query("set character_set_results='utf8'");
    $result = $mysqli->query($sql);
    $rowcount = mysqli_num_rows($result);
    return $rowcount;
}