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
                    array_push($arrayt, $value);
                }

            }
            if (count($arrayt) > 0) {
                return $arrayt;
            }

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
            return $row;
            $arrayt = [];

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
    public function GetSlotsExist($Date)
    {
        $WorkingHours = new WorkingHours();
        $dayofweek = date('w', strtotime($Date));

        $WorkingHours->get_hours_by_day($dayofweek);
        $AppBetweenTimes = array();
        $AppNextTimes = array();
        $LockTimesSlots = array();
        $AllSlotTimesList = array();
        $EndOfAppTimes = array();
        $start = $WorkingHours->openTime;

        $end = $WorkingHours->closeTime;
        //$end = strtotime(convertToHoursMins($WorkingHours->closeTime, '%02d:%02d'));
    
    
        for ($i = $start; $i <= $end; $i += 30) {
            $AllSlotTimesList[] = $i;
        }
        $AllAppointmentsData = $this->GetBooksByDate($Date);

        if ($AllAppointmentsData) {
            foreach ($AllAppointmentsData as $Appointment) {
                $AppStartTimes[] = $Appointment['StartAt'];
                $AppEndTimes[] = $Appointment['StartAt'] + $Appointment['Durtion'];

                //now calculate 5min slots between appointments startAt and EndAt
                $start_et = $Appointment['StartAt'];
                $end_et = $Appointment['StartAt'] + $Appointment['Durtion'];

                for ($i = $start_et; $i < $end_et; $i += 5) //make 15-10=5min slot
                {
                    // $AppBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');
                    $AppBetweenTimes[] = $i;

                    if ($i == $end_et - 5) {
                        $EndOfAppTimes[] = $i + 5;

                        // $EndOfAppTimes[] = convertToHoursMins($i + 5, '%02d:%02d');;
                    }
                }

            }

            //calculating  Next & Previous time of booked appointments
            foreach ($AllSlotTimesList as $single) {
                if (in_array($single, $AppStartTimes)) {
                    //get next time
                    $time = $single;
                    $event_length = 30 - 5; // Service duration time    -  slot time
                    $timestamp = $time;
                    $endtime = $event_length + $timestamp;
                    $next_time = $endtime; //echo "<br>";
                    //calculate next time
                    $start = $single;
                    $end = $next_time;
                    for ($i = $start; $i <= $end; $i += 5) //making 5min diffrance slot
                    {
                        // $AppNextTimes[] = convertToHoursMins($i, '%02d:%02d');

                        $AppNextTimes[] = $i;
                    }

                    //get previous time
                    $time1 = $single;
                    $event_length1 = 30 - 5; // 60min Service duration time - 15 slot time
                    $timestamp1 = $time1;
                    $endtime1 = $timestamp1 - $event_length1;
                    $next_time1 = $endtime1;
                    //calculate previous time
                    $start1 = $next_time1;
                    $end1 = $single;
                    for ($i = $start1; $i <= $end1; $i += 5) //making 5min diff slot
                    {
                        // $AppPreviousTimes[] = convertToHoursMins($i, '%02d:%02d');
                        $AppPreviousTimes[] = $i;

                    }
                }
            }
            //end calculating Next & Previous time of booked appointments

        } // end if $AllAppointmentsData
        $LockTimesSlots = LockHours::get_slots_lock($Date);

        $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppNextTimes,$LockTimesSlots);
        unset($AppBetweenTimes);
        unset($AppNextTimes);
        unset($LockTimesSlots);
        if(isset($DisableSlotsTimes))
            sort($DisableSlotsTimes);
        return ['DisableSlots'=> $DisableSlotsTimes,'End' => $EndOfAppTimes];
    }

    public function get_book_today(){
        $AppointmentDate = date("Y-m-d");
        $todayApps = array();
        $todayApps =  $this->GetBooksByDate($AppointmentDate);
        return count($todayApps);
    }

    public function get_week_book(){
        $dayofweek = date('w', strtotime(date("Y-m-d")));
        
        //this check set the sunday first day in week
        if($dayofweek == 0){
            $startWeek = date("Y-m-d", strtotime('sunday this week'));
            $endWeek = date("Y-m-d", strtotime('friday next week'));
        }
        else{
            $startWeek = date("Y-m-d", strtotime('sunday last week'));
            $endWeek = date("Y-m-d", strtotime('friday this week'));
        }
        $sql = "SELECT * FROM Books WHERE StartDate BETWEEN '$startWeek' AND '$endWeek' ;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            return count($row);

        } catch (PDOException $e) {
            return $e->message();
        }
    }

    public function get_price_month(){
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
            $PriceForAllMonth = (object)$row[0];
            return $PriceForAllMonth;

        } catch (PDOException $e) {
            return $e->message();
        }
    }

}
