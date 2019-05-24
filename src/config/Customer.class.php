<?php 
/**
     * 
     * this class for Customer
     * 
     * Created by Guy Gold. 16/12/2018
     */
    class Customer{
        
        /**
         * Properites
         */
        public $CustomerID;
        public $FirstName;
        public $LastName;
        public $PhoneNumber;

        public function Add(){
            try{
                $resultObj = new ResultAPI();
                $sql = "call CustomerSave('$this->FirstName','$this->LastName','$this->PhoneNumber',@l_CustomerID);";
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':FirstName', $this->FirstName);
                $smst->bindParam(':LastName', $this->LastName);
                $smst->bindParam(':PhoneNumber', $this->PhoneNumber);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            
                $row = $smst->execute();
                $rs2 = $db->query("SELECT @l_CustomerID as id");
                $row2 = $rs2->fetchObject();

                if($row2->id == 501){

                }
                return $row2->id;

            }catch(PDOException $e){
                $var = (string)$e->getMessage();
                echo '{"error": "'.$var.'"}';
            }
        }

        public function GetByPhoneNumber($PhoneNumber,$response){

            $resultObj = new ResultAPI();
            $sql = "call CustomerGetByPhone('$PhoneNumber');";
            try{
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
                $resultObj->set_result($row);
                $resultObj->set_statusCode($response->getStatusCode());
                return json_encode($resultObj,JSON_UNESCAPED_UNICODE);
            }catch(PDOException $e){
                $resultObj->set_ErrorMessage($e->getMessage());
                return json_encode($resultObj,JSON_UNESCAPED_UNICODE);
            }

        }
}