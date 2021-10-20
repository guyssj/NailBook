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
            $sqlquery = "SELECT * FROM LockHours WHERE StartDate > DATE(NOW()-INTERVAL 3 Month)";
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

    public function get_hours_by_date($Date)
    {
        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM LockHours WHERE StartDate='$Date';";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;
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
}
