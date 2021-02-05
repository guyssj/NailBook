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
            $sqlquery = "SELECT * FROM Books";
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
            $sqlquery = "SELECT * FROM Books WHERE BookID='$this->BookID'; ";
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
        $sqlquery = "SELECT * FROM Books WHERE $column BETWEEN '$start' AND '$end' ;";
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

    public function GetBooksByCustomer($CustomerId)
    {
        $sql = "SELECT * FROM Books WHERE CustomerID='$CustomerId';";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            //return $row;
            $date = date('Y-m-d', time());
            $arrayt = [];
            foreach ($row as $key => $value) {
                $strtTime = $value['StartDate'];
                if ($strtTime >= $date) {
                    return $value;
                }
            }
            throw new Exception("Book not Found");
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function GetBookByCustomer($CustomerId)
    {
        $sql = "SELECT * FROM Books WHERE CustomerID='$CustomerId' ORDER BY StartDate ASC;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            //return $row;
            $date = date('Y-m-d', time());
            $arrayt = [];
            foreach ($row as $key => $value) {
                $strtTime = $value['StartDate'];
                if ($strtTime >= $date) {
                    array_push($arrayt, $value);
                }
            }
            if (count($arrayt) > 0) {
                return $arrayt;
            }

            throw new Exception("Book not Found");
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }


    public function AddNotes(Books $Books)
    {
        $sql = "call BookAddNote(:BookID,:Notes);";
        //$sql2 = "SELECT StartDate FROM Books WHERE StartDate='$Books->StartDate' And StartAt='$Books->StartAt' LIMIT 1;";
        try {
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':BookID', $Books->BookID);
            $smst->bindParam(':Notes', $Books->Notes);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute(['BookID' => $Books->BookID, 'Notes' => $Books->Notes]);
            $count = $smst->rowCount();
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }


    public function get_price_month()
    {
        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t');
        $sql = "SELECT sum(st.Price) as PriceForAllMonth from Books bk
        JOIN ServiceType st ON bk.ServiceTypeID = st.ServiceTypeID
        WHERE bk.StartDate BETWEEN '$first_day_this_month' AND '$last_day_this_month';";
        // $sql = "SELECT * FROM Books WHERE StartDate BETWEEN '$startWeek' AND '$endWeek' ;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            $PriceForAllMonth = (object) $row[0];
            if (empty($PriceForAllMonth->PriceForAllMonth)) {
                $PriceForAllMonth->PriceForAllMonth = 0;
            }
            return $PriceForAllMonth;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public function get_price_by_month($month, $year)
    {
        $first_day_this_month = date($year . '-' . $month . '-01'); // hard-coded '01' for first day
        $last_day_this_month  = date($year . '-' . $month . '-t', strtotime($first_day_this_month));
        $sql = "SELECT sum(st.Price) as PriceForAllMonth from Books bk
        JOIN ServiceType st ON bk.ServiceTypeID = st.ServiceTypeID
        WHERE bk.StartDate BETWEEN '$first_day_this_month' AND '$last_day_this_month';";
        // $sql = "SELECT * FROM Books WHERE StartDate BETWEEN '$startWeek' AND '$endWeek' ;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            $PriceForAllMonth = (object) $row[0];
            return $PriceForAllMonth;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
