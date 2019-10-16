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

        public function Add(){
            try {
                $sql = "call ServiceTypeSet('$this->ServiceTypeName','$this->ServiceID','$this->Duration','$this->Price','$this->Description',@l_serviceTypeId);";
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    
                $row = $smst->execute();
                $rs2 = $db->query("SELECT @l_serviceTypeId as id");
                $row2 = $rs2->fetchObject();
                
                return $row2->id;
    
            } catch (PDOException $e) {
                $var = (string) $e->getMessage();
                return $var;
            }
        }

        public static function GetServiceTypeByID2($ServiceID){
            $resultObj = new ResultAPI();
            $sql = "call ServiceTypeByServiceIDGet('$ServiceID');";
            try{
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
                foreach ($row as $key => $value) {
                    if ($value['ServiceID'] == $ServiceID) {
                        return $value;
                    }
                }
            }catch(PDOException $e){
                $resultObj->set_ErrorMessage($e->getMessage());
                return $e;
            }
        }
    }