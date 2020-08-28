<?php

/**
 * 
 * OTP Class
 * 
 * Create by Guy Gold 28/08/2020
 * 
 */
class OTP
{

    //Connection
    private $connection;
    private $dbclass;

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

    public function add($CustomerId)
    {
        $otp = rand(100000,999999);

        try {
            $this->connectDB();
            $sqlquery = "UPDATE Customers SET OTP=$otp WHERE CustomerID='$CustomerId'";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            if($stmt->rowCount() > 0)
                return $otp;
            return 0;



        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
    public function read($CustomerId){

        try {
            $this->connectDB();
            $sqlquery = "SELECT OTP FROM Customers WHERE CustomerID=$CustomerId";
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
}
