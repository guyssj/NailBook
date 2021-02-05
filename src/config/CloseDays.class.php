<?php

/**
 * class for Close Days
 * 
 * Create by Guy Gold 18/11/2019
 * 
 */

namespace BookNail;

use PDOException;

class CloseDays
{

    //Connection
    private $connection;
    private $dbclass;

    public $CloseDayID;
    public $Date;
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
     * read all close days from db
     * 
     * 
     */
    public function read()
    {
        try {
            $this->connectDB();
            //call CloseDaysGetAll() WHERE Date > NOW() ORDER BY Date ASC;
            $sqlquery = "SELECT * FROM CloseDays WHERE Date > NOW() ORDER BY Date ASC";
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

        try {
            $this->connectDB();
            $sqlquery = "call CloseDaysSet(:Date,:Notes);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':Date', $this->Date);
            $stmt->bindParam(':Notes', $this->Notes);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $row = $stmt->rowCount();
            $this->connection = null;
            return $row;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function delete()
    {
        try {
            $this->connectDB();
            $sqlquery = "call CloseDaysDelete(:CloseDaysID);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':CloseDaysID', $this->CloseDayID);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $row = $stmt->rowCount();
            $this->connection = null;
            return $row;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
}
