<?php 
//require '../src/config/ResultsApi.class.php';
//require '../src/config/db.php';
    /**
     * 
     * this class for Books
     * 
     * Created by Guy Gold. 16/12/2018
     */
    class Books{
        
        /**
         * Properites
         */
        public $BookID;
        public $StartDate;
        public $EndDate;
        public $CustomerID;
        public $ServiceID;
        public $Durtion;
        public $ServiceTypeID;

        /**
         * Get All books from Database
         * @var $response
         */
        public function GetBooks($response){
            $resultObj = new ResultAPI();
            $sql = "call BookGetAll();";
            try{
                 $mysqli = new db();
                 $mysqli = $mysqli->connect();
                 $mysqli->query("set character_set_client='utf8'");
                 $mysqli->query("set character_set_results='utf8'");
                 $result = $mysqli->query($sql);
                 $row = cast_query_results($result);
                 $resultObj->set_result($row);
                 $resultObj->set_statusCode($response->getStatusCode());
            }catch(PDOException $e){
                $resultObj->set_ErrorMessage($e->getMessage());
                return json_encode($resultObj,JSON_UNESCAPED_UNICODE);
            }
            return json_encode($resultObj,JSON_UNESCAPED_UNICODE);
        }

        /**
         * @var Books $books
         * 
         * Set book in the db
         */
        public function SetBook(Books $Books){
            $sql = "call BookSet('$Books->StartDate','$Books->EndDate','$Books->CustomerID','$Books->ServiceID','$Books->Durtion','$Books->ServiceTypeID',@l_BookID);";
            $sql2 = "SELECT StartDate FROM Books WHERE StartDate='$Books->StartDate' LIMIT 1;";
            try{
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql2);
                $rowcount = mysqli_num_rows($result);
                if($rowcount > 0){
                    return json_encode(array("message" => "the Book in this time is exsits"));
                    $result->close();
                }
                else{
                    $db = new db();
                    $db = $db->connect2();
                    $smst = $db->prepare($sql);
                    $smst->bindParam(':StartDate', $Books->StartDate);
                    $smst->bindParam(':EndDate', $Books->EndDate);
                    $smst->bindParam(':CustomerID', $Books->CustomerID);
                    $smst->bindParam(':ServiceID', $Books->ServiceID);
                    $smst->bindParam(':Durtion', $Books->Durtion);
                    $db->query("set character_set_client='utf8'");
                    $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                
                    $row = $smst->execute();
                    $rs2 = $db->query("SELECT @l_BookID as id");
                    $row2 = $rs2->fetchObject();
                    return $row2->id;
                }
            }catch(PDOException $e){
                $var = (string)$e->getMessage();
                return '{"error": "'.$var.'"}';
            }
        }

    }