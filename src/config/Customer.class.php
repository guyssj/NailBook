<?php
/**
 *
 * this class for Customer
 *
 * Created by Guy Gold. 16/12/2018
 */


namespace BookNail;

use PDOException;
use Exception;

class Customer
{
        //Connection
        private $connection;
        private $dbclass;

    /**
     * Properites
     */
    public $CustomerID;
    public $FirstName;
    public $LastName;
    public $PhoneNumber;
    public $Color;
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
    

    public function add()
    {
        try {
            $this->connectDB();
            $this->FirstName = str_replace("'", "''", $this->FirstName);
            $this->LastName = str_replace("'", "''", $this->LastName);
            $this->FirstName = str_replace("׳", "''", $this->FirstName);
            $this->LastName = str_replace("׳", "''", $this->LastName);
            $sqlquery = "call CustomerSave(:FirstName,:LastName,:PhoneNumber,:Notes,@l_CustomerID);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':FirstName', $this->FirstName);
            $stmt->bindParam(':LastName', $this->LastName);
            $stmt->bindParam(':PhoneNumber', $this->PhoneNumber);
            $stmt->bindParam(':Notes', $this->Notes);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $rs2 = $this->connection->query("SELECT @l_CustomerID as id");
            $row2 = $rs2->fetchObject();
            $this->connection = null;
            return (int)$row2->id;

        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    /**
     * 
     * read all customers from db
     * 
     * 
     */
    public function read()
    {

        try {
            $this->connectDB();
            $sqlquery = "SELECT CustomerID,FirstName,LastName,PhoneNumber,Color,Notes FROM Customers";
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

    public function update()
    {

        // $sql = "call CustomerUpdate('$this->CustomerID','$this->FirstName','$this->LastName','$this->PhoneNumber');";
        try {
            $this->connectDB();

            $sqlquery = "call CustomerUpdate(:CustomerID,:FirstName,:LastName,:PhoneNumber,:Color,:Notes);";

            $stmt = $this->connection->prepare($sqlquery);

            //fix issue with quets
            $this->FirstName = str_replace("'", "''", $this->FirstName);
            $this->LastName = str_replace("'", "''", $this->LastName);
            $this->FirstName = str_replace("׳", "''", $this->FirstName);
            $this->LastName = str_replace("׳", "''", $this->LastName);
            $stmt->bindParam(':CustomerID', $this->CustomerID);
            $stmt->bindParam(':FirstName', $this->FirstName);
            $stmt->bindParam(':LastName', $this->LastName);
            $stmt->bindParam(':PhoneNumber', $this->PhoneNumber);
            $stmt->bindParam(':Color', $this->Color);
            $stmt->bindParam(':Notes', $this->Notes);


            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            return $stmt;


        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
    //Cast the Fucking Result
    public function cast_query_results($rs)
    {
        $fields = mysqli_fetch_fields($rs);
        $data = array();
        $types = array();
        foreach ($fields as $field) {
            switch ($field->type) {
                case 3:
                    $types[$field->name] = 'int';
                    break;
                case 4:
                    $types[$field->name] = 'float';
                    break;
                default:
                    $types[$field->name] = 'string';
                    break;
            }
        }
        while ($row = mysqli_fetch_assoc($rs)) {
            array_push($data, $row);
        }

        for ($i = 0; $i < count($data); $i++) {
            foreach ($types as $name => $type) {
                settype($data[$i][$name], $type);
            }
        }
        return $data;
    }
}
