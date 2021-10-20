<?php
//require '../src/config/ResultsApi.class.php';
//require '../src/config/db.php';
/**
 *
 * this class for Books
 *
 * Created by Guy Gold. 16/12/2018
 */

namespace BookNail;

use PDOException;
use Exception;

class Books
{
    //Connection
    private $connection;
    private $dbclass;
    /**
     * Properites
     */
    public $BookID;
    public $StartDate;
    //public $EndDate;
    public $StartAt;
    public $CustomerID;
    public $ServiceID;
    public $Durtion;
    public $ServiceTypeID;
    public $Notes;


    public function from_array($array)
    {
        foreach (get_object_vars($this) as $attrName => $attrValue) {
            if (isset($array[$attrName]))
                $this->{$attrName} = $array[$attrName];
        }
    }

    public function __construct()
    {

        $get_arguments = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct' . $number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
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
            $sqlquery = "SELECT BookID,StartDate,StartAt,CustomerID,ServiceID,Durtion,ServiceTypeID,Notes From Books WHERE StartDate > DATE(NOW()-INTERVAL 3 Month)";
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
    /**
     * find a book by ID
     * 
     */
    public function find()
    {

        try {
            $this->connectDB();
            $sqlquery = "SELECT BookID,StartDate,StartAt,CustomerID,ServiceID,Durtion,ServiceTypeID,Notes FROM Books WHERE BookID='$this->BookID'; ";
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
    /**
     * 
     * find a book by date and StartAt
     * 
     * 
     */
    public function find_by_date_at()
    {

        try {
            $this->connectDB();
            $sqlquery = "SELECT StartDate,CustomerID,StartAt FROM Books WHERE StartDate='$this->StartDate' And StartAt='$this->StartAt' LIMIT 1;";
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
    public function today_books()
    {
        try {
            $this->connectDB();
            $sqlquery = "call TodayBooks();";
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
    public function add()
    {
        try {
            $this->connectDB();
            $sqlquery = "call BookSet(:StartDate,:StartAt,:CustomerID,:ServiceID,:Durtion,:ServiceTypeID,@l_BookID);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':StartDate', $this->StartDate);
            $stmt->bindParam(':StartAt', $this->StartAt);
            $stmt->bindParam(':CustomerID', $this->CustomerID);
            $stmt->bindParam(':ServiceID', $this->ServiceID);
            $stmt->bindParam(':Durtion', $this->Durtion);
            $stmt->bindParam(':ServiceTypeID', $this->ServiceTypeID);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $rs2 = $this->connection->query("SELECT @l_BookID as id");
            $row2 = $rs2->fetchObject();
            $this->connection = null;

            return $row2->id;
        } catch (PDOException $e) {
            throw $e->getMessage();
        }
    }
    public function update()
    {
        try {
            $this->connectDB();
            $sqlquery = "call BookUpdate(:StartDate,:StartAt,:BookID,:ServiceID,:ServiceTypeID,:Durtion);";
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':StartDate', $this->StartDate);
            $stmt->bindParam(':StartAt', $this->StartAt);
            $stmt->bindParam(':BookID', $this->BookID);
            $stmt->bindParam(':ServiceID', $this->ServiceID);
            $stmt->bindParam(':ServiceTypeID', $this->ServiceTypeID);
            $stmt->bindParam(':Durtion', $this->Durtion);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;

            return $stmt;
        } catch (PDOException $e) {
            throw $e->getMessage();
        }
    }
    public function delete()
    {
        $sqlquery = "call BookDelete(:BookID);";
        try {
            $this->connectDB();
            $stmt = $this->connection->prepare($sqlquery);
            $stmt->bindParam(':BookID', $this->BookID);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;
        } catch (PDOException $e) {
            throw $e->getMessage();
        }
    }
    public function range($start, $end, $column)
    {
        $sqlquery = "SELECT BookID,StartDate,StartAt,CustomerID,ServiceID,Durtion,ServiceTypeID,Notes FROM Books WHERE $column BETWEEN '$start' AND '$end' ;";
        try {
            $this->connectDB();
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
    public function find_by_customer_id($customerId)
    {
        $sqlquery = "SELECT BookID,StartDate,StartAt,CustomerID,ServiceID,Durtion,ServiceTypeID,Notes FROM Books WHERE CustomerID = $customerId AND StartDate >= DATE(NOW());";
        try {
            $this->connectDB();
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



    public function AddNotes()
    {
        $sql = "call BookAddNote(:BookID,:Notes);";
        try {
            $this->connectDB();
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':BookID', $this->BookID);
            $stmt->bindParam(':Notes', $this->Notes);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute(['BookID' => $this->BookID, 'Notes' => $this->Notes]);
            $this->connection = null;
            return $stmt;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function get_price_by_month($first_day_this_month, $last_day_this_month)
    {

        $sqlquery = "SELECT sum(st.Price) as PriceForAllMonth from Books bk
        JOIN ServiceType st ON bk.ServiceTypeID = st.ServiceTypeID
        WHERE bk.StartDate BETWEEN '$first_day_this_month' AND '$last_day_this_month';";
        // $sql = "SELECT * FROM Books WHERE StartDate BETWEEN '$startWeek' AND '$endWeek' ;";
        try {
            $this->connectDB();
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
