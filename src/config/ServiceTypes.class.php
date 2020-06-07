<?php

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
        $this->dbclass = new db();

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

    public function __construct1($connection)
    {
        $this->connection = $connection;
    }

    public function read()
    {
        try {
            $sqlquery = "SELECT * FROM ServiceType";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function GetServiceTypes()
    {
        $this->connection = $this->dbclass->connect2();
        $ServiceTypes = array();
        try {
            $stmt = $this->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    $p = (object) array(
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "ServiceTypeName" => $ServiceTypeName,
                        "ServiceID" => (int) $ServiceID,
                        "Duration" => (int) $Duration,
                        "Price" => $Price,
                        "Description" => $Description,
                    );

                    array_push($ServiceTypes, $p);
                }
            }
            return $ServiceTypes;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetServiceTypeByID($ID)
    {
        $this->connection = $this->dbclass->connect2();
        $ServiceTypesBySID = array();
        try {
            $stmt = $this->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                if ((int) $ServiceID == $ID) {
                    $p = (object) array(
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "ServiceTypeName" => $ServiceTypeName,
                        "ServiceID" => (int) $ServiceID,
                        "Duration" => (int) $Duration,
                        "Price" => $Price,
                        "Description" => $Description,
                    );

                    array_push($ServiceTypesBySID, $p);
                }
            }
            return $ServiceTypesBySID;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function Add()
    {
        try {
            $sql = "call ServiceTypeSet('$this->ServiceTypeName','$this->ServiceID','$this->Duration','$this->Price','$this->Description',@l_serviceTypeId);";
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);

            $row = $smst->execute();
            $rs2 = $db->query("SELECT @l_serviceTypeId as id");
            $row2 = $rs2->fetchObject();

            return $row2->id;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public static function GetServiceTypeByID2($ServiceID)
    {
        $resultObj = new ResultAPI();
        $sql = "call ServiceTypeByServiceIDGet('$ServiceID');";
        try {
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
        } catch (PDOException $e) {
            $resultObj->set_ErrorMessage($e->getMessage());
            return $e;
        }
    }
}
