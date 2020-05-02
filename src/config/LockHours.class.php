<?php
    class LockHours{

        public $idLockHours;
        public $StartDate;
        public $StartAt;
        public $EndAt;
        public $Notes;

        public function get_all_lock_hours(){
            $sql = "SELECT * FROM LockHours";
            try {
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
                return $row;
    
            } catch (PDOException $e) {
                return $e->message();
            }
        }

        public function add_new_lock_hours(){
            $sql = "call LockHoursAdd(:StartDate,:StartAt,:EndAt,:Notes);";
            try {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':StartDate', $this->StartDate);
                $smst->bindParam(':StartAt', $this->StartAt);
                $smst->bindParam(':EndAt', $this->EndAt);
                $smst->bindParam(':Notes', $this->Notes);

                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute();
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

        public static function get_hours_by_date($Date){
            $sql = "SELECT * FROM LockHours WHERE StartDate='$Date';";
            try {
                $mysqli = new db();
                $mysqli = $mysqli->connect();
                $mysqli->query("set character_set_client='utf8'");
                $mysqli->query("set character_set_results='utf8'");
                $result = $mysqli->query($sql);
                $row = cast_query_results($result);
                return $row;
    
            } catch (PDOException $e) {
                return $e->message();
            }
        }

        public static function delete_lock_hours($id){
            $sql = "call LockHoursDelete(:idLockHours);";
            try {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':idLockHours', $id);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute(['idLockHours' => $id]);
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

        public static function get_slots_lock($Date){
            $EventBetweenTimes = array();
            $LockHoursData = LockHours::get_hours_by_date($Date);
            if ($LockHoursData) {
                foreach ($LockHoursData as $LockHour) {
                    $start_et = $LockHour['StartAt'];
                    $end_et = $LockHour['EndAt'];
                    for ($i = $start_et; $i < $end_et; $i += 5) //making 5min slot
                    {
                        // $EventBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');
                        $EventBetweenTimes[] = $i;
        
                    }
                }
            }
            return $EventBetweenTimes;
        }

    }