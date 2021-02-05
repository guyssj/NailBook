<?php
    /**
     * 
     * this class for Working Hours
     * 
     * Created by Guy Gold. 06/11/2019
     */


    namespace BookNail;

    use PDOException;

    class WorkingHours{

        public $dayOfWeek;
        public $openTime;
        public $closeTime;

        public function get_all_hours(){
            $sql = "call WorkHoursGetAll();";
            try {
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
            } catch (PDOException $e) {
                $var = (string) $e->getMessage();
                return $var;
            }

            return $row;
        }

        public function get_hours_by_day($dayOfWeek){
            $sql = "call WorkHoursGetAll();";
            try {
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
                foreach ($row as $key => $value) {
                    if ($value['DayOfWeek'] == $dayOfWeek) {
                        $this->dayOfWeek = $value['DayOfWeek'];
                        $this->openTime = $value['OpenTime'];
                        $this->closeTime = $value['CloseTime'];
                        return $value;
                    }
                }
                return $this;
            } catch (PDOException $e) {
                //$resultObj->set_ErrorMessage($e->getMessage());
                return json_encode($e, JSON_UNESCAPED_UNICODE);
            }
        }

        public function set_workingHours(){
            $sql = "call WorkingHoursSet(:DayOfWeek,:OpenTime,:CloseTime);";
            try {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':DayOfWeek', $this->dayOfWeek);
                $smst->bindParam(':OpenTime', $this->openTime);
                $smst->bindParam(':CloseTime', $this->closeTime);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute(['DayOfWeek' => $this->dayOfWeek, 'OpenTime' => $this->openTime,'CloseTime' => $this->closeTime]);
                $count = $smst->rowCount();
                if($count > 0){
                    return true;
                }
                else{
                    return false;
                }
            } catch (PDOException $e) {
                $var = (string) $e->getMessage();
                return $var;
            }
        }

        public function update_workingHours(){
            $sql = "call WorkingHoursUpdate(:DayOfWeek,:OpenTime,:CloseTime);";
            try {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':DayOfWeek', $this->dayOfWeek);
                $smst->bindParam(':OpenTime', $this->openTime);
                $smst->bindParam(':CloseTime', $this->closeTime);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute(['DayOfWeek' => $this->dayOfWeek, 'OpenTime' => $this->openTime,'CloseTime' => $this->closeTime]);
                $count = $smst->rowCount();
                if($count > 0){
                    return true;
                }
                else{
                    return false;
                }
            } catch (PDOException $e) {
                $var = (string) $e->getMessage();
                return $var;
            }
        }
    }