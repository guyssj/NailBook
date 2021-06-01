<?php

/**
 * class for Holidays
 * 
 * Create by Guy Gold 21/02/2020
 * 
 */

namespace BookNail;

use PDOException;

class Holidays
{
    //Connection
    private $connection;
    private $dbclass;
    public $HolidayID;
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
     * read all holidays from db
     * 
     * 
     */
    public function read()
    {

        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM Holidays WHERE Date > NOW() ORDER BY Date ASC";
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
        $sql = "INSERT INTO Holidays (Date,Notes) VALUES (:Date,:Notes);";
        try {
            $this->connectDB();

            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':Date', $this->Date);
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
}
