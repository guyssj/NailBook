<?php


namespace BookNail;

use PDOException;

class LockHours
{


    //Connection
    private $connection;
    private $dbclass;

    public $idLockHours;
    public $StartDate;
    public $StartAt;
    public $EndAt;
    public $Notes;


    public function from_array($array)
    {
        foreach (get_object_vars($this) as $attrName => $attrValue) {
            if (isset($array[$attrName]))
                $this->{$attrName} = $array[$attrName];
        }
    }

    public function connectDB()
    {
        $this->dbclass = new db();
        $this->connection = $this->dbclass->connect2();
    }

    /**
     * 
     * Read from db
     */
    public function read()
    {
        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM LockHours WHERE StartDate > DATE(NOW()-INTERVAL 6 Month)";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;
        } catch (PDOException $e) {
            throw $e->getMessage();
        }
    }

    public function add()
    {
        $sql = "call LockHoursAdd(:StartDate,:StartAt,:EndAt,:Notes);";
        try {
            $this->connectDB();

            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':StartDate', $this->StartDate);
            $stmt->bindParam(':StartAt', $this->StartAt);
            $stmt->bindParam(':EndAt', $this->EndAt);
            $stmt->bindParam(':Notes', $this->Notes);

            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;

        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public static function get_hours_by_date($Date)
    {
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
            return $e->getMessage();
        }
    }

    public function delete($id)
    {
        $sql = "call LockHoursDelete(:idLockHours);";
        try {
            $this->connectDB();

            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':idLockHours',$id);

            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;

        }  catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public static function get_slots_lock($Date)
    {
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
