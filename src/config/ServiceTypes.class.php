<?php

    class ServiceTypes{

        //Properties
        public $ServiceTypeID;
        public $ServiceTypeName;
        public $ServiceID;
        public $Duration;
        public $Price;
        public $Description;


        public function GetServiceTypes($response){
            $resultObj = new ResultAPI();
            $sql = "SELECT * FROM ServiceType";
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

        public function GetServiceTypeByID($ServiceID,$response){
            $resultObj = new ResultAPI();
            $sql = "call ServiceTypeByServiceIDGet('$ServiceID');";
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