<?php


namespace BookNail;

use PDOException;


class ServiceTypes
{

    //Connection
    private $connection;
    private $dbclass;

    //

    //Properties
    public $ServiceTypeID;
    public $ServiceTypeName;
    public $ServiceID;
    public $Duration;
    public $Price;
    public $Description;

    public function __construct()
    {
        $get_arguments = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct' . $number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }
    public function from_array($array)
    {
        foreach (get_object_vars($this) as $attrName => $attrValue)
            $this->{$attrName} = $array[$attrName];
    }

    public function connectDB()
    {
        $this->dbclass = new db();
        $this->connection = $this->dbclass->connect2();
    }

    public function read()
    {
        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM ServiceType";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function add()
    {

        try {
            $this->connectDB();

            $sqlquery = "call ServiceTypeSet(:ServiceTypeName,:ServiceID,:Duration,:Price,:Description,@l_serviceTypeId);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':ServiceTypeName', $this->ServiceTypeName);
            $stmt->bindParam(':ServiceID', $this->ServiceID);
            $stmt->bindParam(':Duration', $this->Duration);
            $stmt->bindParam(':Price', $this->Price);
            $stmt->bindParam(':Description', $this->Description);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $rs2 = $this->connection->query("SELECT @l_serviceTypeId as id");
            $row2 = $rs2->fetchObject();
            $this->connection = null;
            return (int)$row2->id;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
}
