<?php
class Devices {

    //Connection
    private $connection;
    private $dbclass;


    //prop

    public $regId;
    public $deviceType;
    public $userName;

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

    public function add_regId()
    {
        try {
            $this->connectDB();
            $sqlquery = "UPDATE Users SET RegId='$this->regId' WHERE UserName='$this->userName'";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            if ($stmt->rowCount() > 0)
                return true;
            return false;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function update_user(){
        try {
            $this->connectDB();
            $sqlquery = "UPDATE Devices SET='$this->userId' WHERE RegistrationId='$this->regId";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            if ($stmt->rowCount() > 0)
                return true;
            return false;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
}
?>