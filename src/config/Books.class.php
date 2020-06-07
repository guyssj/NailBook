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
    /**
     * Get All books from Database
     * @var $response
     */

    // public function GetBooks()
    // {
    //     $this->connection = $this->dbclass->connect2();
    //     $Books = array();
    //     try {
    //         $stmt = $this->read();
    //         $count = $stmt->rowCount();
    //         if ($count > 0) {
    //             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //                 extract($row);
    //                 $p = (object) array(
    //                     "BookID" => (int) $BookID,
    //                     "StartDate" => $StartDate,
    //                     "StartAt" => (int) $StartAt,
    //                     "CustomerID" => (int) $CustomerID,
    //                     "ServiceID" => (int) $ServiceID,
    //                     "Durtion" => (int) $Durtion,
    //                     "ServiceTypeID" => (int) $ServiceTypeID,
    //                     "Notes" => $Notes,
    //                 );
    //                 array_push($Books, $p);
    //             }
    //         }
    //         return $Books;
    //     } catch (Exception $e) {
    //         throw $e;
    //     }
    // }

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

    // /**
    //  * Get Books By date
    //  *
    //  * @param $Date - date of books
    //  *
    //  * @return Array of books
    //  */
    // public function GetBooksByDate($Date)
    // {
    //     $sql = "SELECT * FROM Books WHERE StartDate='$Date' ORDER BY StartAt ASC;";
    //     try {
    //         $mysqli = new db();
    //         $mysqli = $mysqli->connect();
    //         $mysqli->query("set character_set_client='utf8'");
    //         $mysqli->query("set character_set_results='utf8'");
    //         $result = $mysqli->query($sql);
    //         $row = cast_query_results($result);
    //         return $row;
    //     } catch (PDOException $e) {
    //         return $e->getMessage();
    //     }
    // }
    /**
     * @var Books $books
     *
     * Set book in the db
     */
    // public function SetBook()
    // {
    //     $sql = "call BookSet(:StartDate,:StartAt,:CustomerID,:ServiceID,:Durtion,:ServiceTypeID,@l_BookID);";
    //     $sql2 = "SELECT StartDate FROM Books WHERE StartDate='$this->StartDate' And StartAt='$this->StartAt' LIMIT 1;";
    //     try {
    //         $mysqli = new db();
    //         $mysqli = $mysqli->connect();
    //         $mysqli->query("set character_set_client='utf8'");
    //         $mysqli->query("set character_set_results='utf8'");
    //         $result = $mysqli->query($sql2);
    //         $rowcount = mysqli_num_rows($result);
    //         if ($rowcount > 0) {
    //             $result->close();
    //             return -1;
    //         } else {
    //             $db = new db();
    //             $db = $db->connect2();
    //             $smst = $db->prepare($sql);
    //             $smst->bindParam(':StartDate', $this->StartDate);
    //             $smst->bindParam(':StartAt', $this->StartAt);
    //             $smst->bindParam(':CustomerID', $this->CustomerID);
    //             $smst->bindParam(':ServiceID', $this->ServiceID);
    //             $smst->bindParam(':Durtion', $this->Durtion);
    //             $smst->bindParam(':ServiceTypeID', $this->ServiceTypeID);
    //             $db->query("set character_set_client='utf8'");
    //             $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    //             $row = $smst->execute();
    //             $rs2 = $db->query("SELECT @l_BookID as id");
    //             $row2 = $rs2->fetchObject();
    //             return $row2->id;
    //         }
    //     } catch (PDOException $e) {
    //         $var = (string) $e->getMessage();
    //         return $var;
    //     }
    // }

    /**
     *
     * Updated Books
     *
     * @param $Books
     */
    // public function UpdateBook()
    // {
    //     $sql = "call BookUpdate(:StartDate,:StartAt,:BookID,:ServiceID,:ServiceTypeID,:Durtion);";
    //     $sql2 = "SELECT StartDate,CustomerID,StartAt FROM Books WHERE StartDate='$this->StartDate' And StartAt='$this->StartAt' LIMIT 1;";
    //     try {
    //         $mysqli = new db();
    //         $mysqli = $mysqli->connect();
    //         $mysqli->query("set character_set_client='utf8'");
    //         $mysqli->query("set character_set_results='utf8'");
    //         $result = $mysqli->query($sql2);
    //         $rowcount = mysqli_num_rows($result);
    //         $row = cast_query_results($result);
    //         if ($rowcount > 0) {
    //             foreach ($row as $key => $value) {
    //                 if (
    //                     $value['StartAt'] == $this->StartAt
    //                     && $value['CustomerID'] == $this->CustomerID
    //                 ) {
    //                     $db = new db();
    //                     $db = $db->connect2();
    //                     $smst = $db->prepare($sql);
    //                     $smst->bindParam(':StartDate', $this->StartDate);
    //                     $smst->bindParam(':StartAt', $this->StartAt);
    //                     $smst->bindParam(':BookID', $this->BookID);
    //                     $smst->bindParam(':ServiceID', $this->ServiceID);
    //                     $smst->bindParam(':ServiceTypeID', $this->ServiceTypeID);
    //                     $smst->bindParam(':Durtion', $this->Durtion);

    //                     $db->query("set character_set_client='utf8'");
    //                     $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    //                     $row = $smst->execute();
    //                     $row = $smst->rowCount();
    //                     $ro2 = $smst->fetchAll(PDO::FETCH_ASSOC);
    //                     //$ro2 = cast_query_results($ro2);
    //                     foreach ($ro2 as $value) {
    //                         return $value;
    //                     }
    //                 }
    //             }
    //             return null;
    //         } else {
    //             $db = new db();
    //             $db = $db->connect2();
    //             $smst = $db->prepare($sql);
    //             $smst->bindParam(':StartDate', $this->StartDate);
    //             $smst->bindParam(':StartAt', $this->StartAt);
    //             $smst->bindParam(':BookID', $this->BookID);
    //             $smst->bindParam(':ServiceID', $this->ServiceID);
    //             $smst->bindParam(':ServiceTypeID', $this->ServiceTypeID);
    //             $smst->bindParam(':Durtion', $this->Durtion);

    //             $db->query("set character_set_client='utf8'");
    //             $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    //             $row = $smst->execute();
    //             $row = $smst->rowCount();
    //             $ro2 = $smst->fetchAll(PDO::FETCH_ASSOC);
    //             //$ro2 = cast_query_results($ro2);
    //             foreach ($ro2 as $value) {
    //                 return $value;
    //             }
    //         }
    //     } catch (PDOException $e) {
    //         $var = (string) $e->getMessage();
    //         return $var;
    //     }
    // }


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

    /**
     *   Fetch All today's appointments and calculate disable slots
     */
    // public function GetSlotsExist($Date)
    // {
    //     $WorkingHours = new WorkingHours();
    //     $dayofweek = date('w', strtotime($Date));

    //     $WorkingHours->get_hours_by_day($dayofweek);
    //     $AppBetweenTimes = array();
    //     $AppNextTimes = array();
    //     $LockTimesSlots = array();
    //     $AllSlotTimesList = array();
    //     $EndOfAppTimes = array();
    //     $start = $WorkingHours->openTime;

    //     $end = $WorkingHours->closeTime;
    //     //$end = strtotime(convertToHoursMins($WorkingHours->closeTime, '%02d:%02d'));


    //     for ($i = $start; $i <= $end; $i += 30) {
    //         $AllSlotTimesList[] = $i;
    //     }
    //     $AllAppointmentsData = $this->GetBooksByDate($Date);

    //     if ($AllAppointmentsData) {
    //         foreach ($AllAppointmentsData as $Appointment) {
    //             $AppStartTimes[] = $Appointment['StartAt'];
    //             $AppEndTimes[] = $Appointment['StartAt'] + $Appointment['Durtion'];

    //             //now calculate 5min slots between appointments startAt and EndAt
    //             $start_et = $Appointment['StartAt'];
    //             $end_et = $Appointment['StartAt'] + $Appointment['Durtion'];
    //             for ($i = $start_et; $i < $end_et; $i += 5) //make 15-10=5min slot
    //             {
    //                 // if ($i == $start_et){
    //                 //    $count = count($AppBetweenTimes)-1;

    //                 //    if ($count > -1 && $i-5 != $AppBetweenTimes[$count]){
    //                 //     $EndOfAppTimes[] = $i - 5;
    //                 //    }
    //                 // }
    //                 $AppBetweenTimes[] = $i;
    //                 // $AppBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');

    //                 if ($i == $end_et - 5) {
    //                     $EndOfAppTimes[] = $i + 5;
    //                 }
    //             }
    //         }

    //         //calculating  Next & Previous time of booked appointments
    //         foreach ($AllSlotTimesList as $single) {
    //             if (in_array($single, $AppStartTimes)) {
    //                 //get next time
    //                 $time = $single;
    //                 $event_length = 30 - 5; // Service duration time    -  slot time
    //                 $timestamp = $time;
    //                 $endtime = $event_length + $timestamp;
    //                 $next_time = $endtime; //echo "<br>";
    //                 //calculate next time
    //                 $start = $single;
    //                 $end = $next_time;
    //                 for ($i = $start; $i <= $end; $i += 5) //making 5min diffrance slot
    //                 {
    //                     // $AppNextTimes[] = convertToHoursMins($i, '%02d:%02d');

    //                     $AppNextTimes[] = $i;
    //                 }

    //                 //get previous time
    //                 $time1 = $single;
    //                 $event_length1 = 30 - 5; // 60min Service duration time - 15 slot time
    //                 $timestamp1 = $time1;
    //                 $endtime1 = $timestamp1 - $event_length1;
    //                 $next_time1 = $endtime1;
    //                 //calculate previous time
    //                 $start1 = $next_time1;
    //                 $end1 = $single;
    //                 for ($i = $start1; $i <= $end1; $i += 5) //making 5min diff slot
    //                 {
    //                     // $AppPreviousTimes[] = convertToHoursMins($i, '%02d:%02d');
    //                     $AppPreviousTimes[] = $i;
    //                 }
    //             }
    //         }
    //         //end calculating Next & Previous time of booked appointments

    //     } // end if $AllAppointmentsData
    //     $LockTimesSlots = LockHours::get_slots_lock($Date);

    //     $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppNextTimes, $LockTimesSlots);
    //     unset($AppBetweenTimes);
    //     unset($AppNextTimes);
    //     unset($LockTimesSlots);
    //     if (isset($DisableSlotsTimes))
    //         sort($DisableSlotsTimes);
    //     return ['DisableSlots' => $DisableSlotsTimes, 'End' => $EndOfAppTimes];
    // }

    /**
     *   Fetch All today's appointments and calculate disable slots
     */
    // public function GetSlotsExistForLock($Date)
    // {
    //     $WorkingHours = new WorkingHours();
    //     $dayofweek = date('w', strtotime($Date));

    //     $WorkingHours->get_hours_by_day($dayofweek);
    //     $AppBetweenTimes = array();
    //     $AppNextTimes = array();
    //     $LockTimesSlots = array();
    //     $AllSlotTimesList = array();
    //     $EndOfAppTimes = array();
    //     $start = $WorkingHours->openTime;

    //     $end = $WorkingHours->closeTime;
    //     //$end = strtotime(convertToHoursMins($WorkingHours->closeTime, '%02d:%02d'));


    //     for ($i = $start; $i <= $end; $i += 30) {
    //         $AllSlotTimesList[] = $i;
    //     }
    //     $AllAppointmentsData = $this->GetBooksByDate($Date);

    //     if ($AllAppointmentsData) {
    //         foreach ($AllAppointmentsData as $Appointment) {
    //             $AppStartTimes[] = $Appointment['StartAt'];
    //             $AppEndTimes[] = $Appointment['StartAt'] + $Appointment['Durtion'];

    //             //now calculate 5min slots between appointments startAt and EndAt
    //             $start_et = $Appointment['StartAt'];
    //             $end_et = $Appointment['StartAt'] + $Appointment['Durtion'];
    //             for ($i = $start_et; $i < $end_et; $i += 5) //make 15-10=5min slot
    //             {
    //                 if ($i == $start_et) {
    //                     $count = count($AppBetweenTimes) - 1;

    //                     if ($count > -1 && $i - 5 != $AppBetweenTimes[$count]) {
    //                         $EndOfAppTimes[] = $i - 5;
    //                     }
    //                 }
    //                 $AppBetweenTimes[] = $i;
    //                 // $AppBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');

    //                 if ($i == $end_et - 5) {
    //                     $EndOfAppTimes[] = $i + 5;
    //                 }
    //             }
    //         }

    //         //calculating  Next & Previous time of booked appointments
    //         foreach ($AllSlotTimesList as $single) {
    //             if (in_array($single, $AppStartTimes)) {
    //                 //get next time
    //                 $time = $single;
    //                 $event_length = 30 - 5; // Service duration time    -  slot time
    //                 $timestamp = $time;
    //                 $endtime = $event_length + $timestamp;
    //                 $next_time = $endtime; //echo "<br>";
    //                 //calculate next time
    //                 $start = $single;
    //                 $end = $next_time;
    //                 for ($i = $start; $i <= $end; $i += 5) //making 5min diffrance slot
    //                 {
    //                     // $AppNextTimes[] = convertToHoursMins($i, '%02d:%02d');

    //                     $AppNextTimes[] = $i;
    //                 }

    //                 //get previous time
    //                 $time1 = $single;
    //                 $event_length1 = 30 - 5; // 60min Service duration time - 15 slot time
    //                 $timestamp1 = $time1;
    //                 $endtime1 = $timestamp1 - $event_length1;
    //                 $next_time1 = $endtime1;
    //                 //calculate previous time
    //                 $start1 = $next_time1;
    //                 $end1 = $single;
    //                 for ($i = $start1; $i <= $end1; $i += 5) //making 5min diff slot
    //                 {
    //                     // $AppPreviousTimes[] = convertToHoursMins($i, '%02d:%02d');
    //                     $AppPreviousTimes[] = $i;
    //                 }
    //             }
    //         }
    //         //end calculating Next & Previous time of booked appointments

    //     } // end if $AllAppointmentsData
    //     $LockTimesSlots = LockHours::get_slots_lock($Date);

    //     $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppNextTimes, $LockTimesSlots);
    //     unset($AppBetweenTimes);
    //     unset($AppNextTimes);
    //     unset($LockTimesSlots);
    //     if (isset($DisableSlotsTimes))
    //         sort($DisableSlotsTimes);
    //     return ['DisableSlots' => $DisableSlotsTimes, 'End' => $EndOfAppTimes];
    // }


    // public function get_week_book()
    // {
    //     $dayofweek = date('w', strtotime(date("Y-m-d")));

    //     //this check set the sunday first day in week
    //     if ($dayofweek == 0) {
    //         $startWeek = date("Y-m-d", strtotime('sunday this week'));
    //         $endWeek = date("Y-m-d", strtotime('friday next week'));
    //     } else {
    //         $startWeek = date("Y-m-d", strtotime('sunday last week'));
    //         $endWeek = date("Y-m-d", strtotime('friday this week'));
    //     }
    //     $sql = "SELECT * FROM Books WHERE StartDate BETWEEN '$startWeek' AND '$endWeek' ;";
    //     try {
    //         $mysqli = new db();
    //         $mysqli = $mysqli->connect();
    //         $mysqli->query("set character_set_client='utf8'");
    //         $mysqli->query("set character_set_results='utf8'");
    //         $result = $mysqli->query($sql);
    //         $row = cast_query_results($result);
    //         return count($row);
    //     } catch (PDOException $e) {
    //         return $e->getMessage();
    //     }
    // }

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
