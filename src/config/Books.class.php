<?php
//require '../src/config/ResultsApi.class.php';
//require '../src/config/db.php';
/**
 *
 * this class for Books
 *
 * Created by Guy Gold. 16/12/2018
 */
class Books
{

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

    /**
     * Get All books from Database
     * @var $response
     */
    public function GetBooks($response)
    {
        $resultObj = new ResultAPI();
        $sql = "call BookGetAll();";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            $resultObj->set_result($row);
            $resultObj->set_statusCode($response->getStatusCode());
        } catch (PDOException $e) {
            $resultObj->set_ErrorMessage($e->getMessage());
            return json_encode($resultObj, JSON_UNESCAPED_UNICODE);
        }
        return json_encode($resultObj, JSON_UNESCAPED_UNICODE);
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
            return $e->message();
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
                    array_push($arrayt,$value);
                }

            }
            if(count($arrayt) > 0)
                return $arrayt;
            throw new Exception("Book not Found");
            

        } catch (PDOException $e) {
            return $e->message();
        }
    }

    /**
     * Get Books By date
     *
     * @param $Date - date of books
     *
     * @return Array of books
     */
    public function GetBooksByDate($Date)
    {
        $sql = "SELECT * FROM Books WHERE StartDate='$Date';";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            //return $row;
            $arrayt = [];
            foreach ($row as $key => $value) {
                $strtTime = $value['StartAt'];
                $endTime = $value['StartAt'] + $value['Durtion'];
                for ($i = $strtTime; $i <= $endTime; $i = $i + 5) {
                    array_push($arrayt, $i);
                }

            }
            array_push($arrayt,$endTime+5);
            return $arrayt;

        } catch (PDOException $e) {
            return $e->message();
        }
    }
    /**
     * @var Books $books
     *
     * Set book in the db
     */
    public function SetBook(Books $Books)
    {
        $sql = "call BookSet('$Books->StartDate','$Books->StartAt','$Books->CustomerID','$Books->ServiceID','$Books->Durtion','$Books->ServiceTypeID',@l_BookID);";
        $sql2 = "SELECT StartDate FROM Books WHERE StartDate='$Books->StartDate' And StartAt='$Books->StartAt' LIMIT 1;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql2);
            $rowcount = mysqli_num_rows($result);
            if ($rowcount > 0) {
                return -1;
                $result->close();
            } else {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':StartDate', $Books->StartDate);
                $smst->bindParam(':StartAt', $Books->StartAt);
                $smst->bindParam(':CustomerID', $Books->CustomerID);
                $smst->bindParam(':ServiceID', $Books->ServiceID);
                $smst->bindParam(':Durtion', $Books->Durtion);
                $smst->bindParam(':ServiceTypeID', $Books->ServiceTypeID);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute();
                $rs2 = $db->query("SELECT @l_BookID as id");
                $row2 = $rs2->fetchObject();
                return $row2->id;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    /**
     *
     * Updated Books
     *
     * @param $Books
     */
    public function UpdateBook(Books $Books)
    {
        $sql = "call BookUpdate(:StartDate,:StartAt,:BookID);";
        $sql2 = "SELECT StartDate FROM Books WHERE StartDate='$Books->StartDate' And StartAt='$Books->StartAt' LIMIT 1;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql2);
            $rowcount = mysqli_num_rows($result);
            if ($rowcount > 0) {
                return null;
                $result->close();
            } else {
                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':StartDate', $Books->StartDate);
                $smst->bindParam(':StartAt', $Books->StartAt);
                $smst->bindParam(':BookID', $Books->BookID);
                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute(['StartDate' => $Books->StartDate, 'StartAt' => $Books->StartAt, 'BookID' => $Books->BookID]);
                $row = $smst->rowCount();
                $ro2 = $smst->fetchAll(PDO::FETCH_ASSOC);
                //$ro2 = cast_query_results($ro2);
                foreach ($ro2 as $value) {
                    return $value;
                }

            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function DeleteBook(Books $Books)
    {
        $sql = "call BookDelete(:BookID);";
        //$sql2 = "SELECT StartDate FROM Books WHERE StartDate='$Books->StartDate' And StartAt='$Books->StartAt' LIMIT 1;";
        try {
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':BookID', $Books->BookID);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute(['BookID' => $Books->BookID]);
            $count = $smst->rowCount();
            if($count > 0){
                return true;
            }
            else{
                return false;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
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
            if($count > 0){
                return true;
            }
            else{
                return false;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

}
