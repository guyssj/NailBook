<?php

/**
 * 
 * Service class 
 * 
 * Created by guy gold 24/6/2019
 */


namespace BookNail;

use PDOException;

class Services
{
    //Connection
    private $connection;
    private $dbclass;

    private $ServiceID;
    private $ServiceName;


    //Methods

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
    /**
     * 
     * Get all service from database
     */
    public function read()
    {
        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM Services";
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
}
