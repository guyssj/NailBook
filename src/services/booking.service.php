<?php

namespace BookNail;

use Exception;
use PDO;
use DateTime;
use DateTimeZone;
use Slim\Http\Request as Request;

class BookingService
{

    /**
     * @var Books $book
     *
     * Set book in the db
     */
    public static function SetBook(Books $book)
    {
        if ($book->find_by_date_at()->rowCount() > 0)
            throw new Exception("Treatment is exists in this time", 409);
        if ($book->add() > 0) {
            //send SMS after booking set
            $SendSMS = Settings::get_Setting(Settings::SEND_SMS_APP)['SettingValue'];
            if ($SendSMS == "1") {
                $customer = CustomersService::find_customer_by_id($book->CustomerID);
                $ServiceType = ServiceTypesService::get_service_type_by_id($book->ServiceTypeID);
                $globalSMS = new globalSMS();
                $Date = strtotime($book->StartDate);
                $NewDate = date("d/m/Y", $Date);
                $Time = $book->StartAt;
                $newTime = hoursandmins($Time);
                $message = Settings::get_Setting(Settings::SMS_TEMPLATE_APP)['SettingValue'];
                $message = str_replace('\n', PHP_EOL, $message);
                $message = str_replace('{FirstName}', $customer->FirstName, $message);
                $message = str_replace('{LastName}', $customer->LastName, $message);
                $message = str_replace('{Date}', $NewDate, $message);
                $message = str_replace('{Time}', $newTime, $message);
                $message = str_replace('{ServiceType}', $ServiceType->ServiceTypeName, $message);
                $globalSMS->send_sms($customer->PhoneNumber, $message);
                $regId = UsersService::get_regId_by_userName("mirit");
                // Here, INCLUDE YOUR FCM FILE
                $arrNotification = array();
                $arrData = array();

                $arrData["StartDate"] = $Date;
                $arrNotification["body"] = "פגישה נקבעה ללקוח/ה $customer->FirstName $customer->LastName בתאריך $NewDate בשעה $newTime";
                $arrNotification["title"] = "פגישה נקבעה";
                $arrNotification["click_action"] = "FCM_PLUGIN_ACTIVITY";
                $fcm = new FCM();
                $result = $fcm->send_notification($regId, $arrNotification, $arrData, "Android");
            }
            return true;
        }
        return false;
    }
    /**
     * update book
     * 
     * @var Books $book
     */
    public static function update_book(Books $book)
    {
        $stmt = $book->find_by_date_at();
        if ($stmt->rowCount() > 0) {
            $booksFind = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($booksFind as $value) {
                if (
                    $value['StartAt'] == $book->StartAt
                    && $value['CustomerID'] == $book->CustomerID
                ) {
                    if ($book->update()->rowCount() > 0) {
                        self::generate_message($book, Settings::SMS_TEMPLATE_UPAPP, "עידכון פגישה");
                        return true;
                    } else
                        return false;
                }
            }
        } else {
            if ($book->update()->rowCount() > 0) {
                self::generate_message($book, Settings::SMS_TEMPLATE_UPAPP, "עידכון פגישה");
                return true;
            } else
                return false;
        }
    }

    /**
     * 
     * get all book
     * 
     * @return array[Books]
     */
    public static function get_books()
    {
        $BooksObj = new Books();
        $Books = array();
        try {
            $stmt = $BooksObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $ServiceType = new ServiceTypes();

                    //Orignial book from db
                    $Book = (object) array(
                        "BookID" => (int) $BookID,
                        "StartDate" => $StartDate,
                        "StartAt" => (int) $StartAt,
                        "CustomerID" => (int) $CustomerID,
                        "ServiceID" => (int) $ServiceID,
                        "Durtion" => (int) $Durtion,
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "Notes" => $Notes,
                    );

                    $Customer = CustomersService::find_customer_by_id($Book->CustomerID);
                    $ServiceType = ServiceTypesService::get_service_type_by_id($Book->ServiceTypeID);

                    //set the time for book
                    $startTime = new DateTime($Book->StartDate, new DateTimeZone('Asia/Jerusalem'));
                    $startTime->modify("+{$Book->StartAt} minutes");
                    $endTime = new DateTime($Book->StartDate, new DateTimeZone('Asia/Jerusalem'));
                    $totalTime = $Book->Durtion + $Book->StartAt;
                    $endTime->modify("+{$totalTime} minutes");

                    $endTime = $endTime->format('c');
                    $startTime = $startTime->format('c');

                    //object for clendar ionic
                    $p = (object) array(
                        "title" => "{$Customer->FirstName} {$Customer->LastName} - {$ServiceType->ServiceTypeName}",
                        "allDay" => false,
                        "endTime" => $endTime,
                        "startTime" => $startTime,
                        "meta" => (object) $Book,
                        "customer" => (object) $Customer,
                        "serviceType" => (object) $ServiceType,
                    );
                    array_push($Books, $p);
                }
            }
            return $Books;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get books by Customer ID
     * 
     * @return array[Books]
     */
    public static function get_books_by_customerId($cusId)
    {
        $books = new Books();
        $BooksCustomer = array();
        try {
            $stmt = $books->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $date = date('Y-m-d', time());
                if ((int) $CustomerID == $cusId && $StartDate >= $date) {
                    $p = (object) array(
                        "BookID" => (int) $BookID,
                        "StartDate" => $StartDate,
                        "StartAt" => (int) $StartAt,
                        "CustomerID" => (int) $CustomerID,
                        "ServiceID" => (int) $ServiceID,
                        "Durtion" => (int) $Durtion,
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "Notes" => $Notes,
                    );

                    array_push($BooksCustomer, $p);
                }
            }
            if (count($BooksCustomer) == 0)
                throw new Exception("Book not found", 404);

            BookingService::array_sort_by_column($BooksCustomer, 'StartDate');
            return $BooksCustomer;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get books by Customer PhoneNumber
     * 
     * @return array[Books]
     */
    public static function get_books_by_phoneNumber($phoneNumber)
    {
        $cusId = CustomersService::find_customer_id_by_phone($phoneNumber);
        if (!isset($cusId))
            throw new Exception("Customer not found", 404);
        $books = new Books();
        $BooksCustomer = array();
        try {
            $stmt = $books->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $date = date('Y-m-d', time());
                if ((int) $CustomerID == $cusId && $StartDate >= $date) {
                    $p = (object) array(
                        "BookID" => (int) $BookID,
                        "StartDate" => $StartDate,
                        "StartAt" => (int) $StartAt,
                        "CustomerID" => (int) $CustomerID,
                        "ServiceID" => (int) $ServiceID,
                        "Durtion" => (int) $Durtion,
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "Notes" => $Notes,
                    );

                    array_push($BooksCustomer, $p);
                }
            }
            if (count($BooksCustomer) == 0)
                throw new Exception("Book not found", 404);

            BookingService::array_sort_by_column($BooksCustomer, 'StartDate');
            return $BooksCustomer;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Find books with date range
     * @return array[Books]
     */
    public static function find_books_by_date_range($start, $end)
    {
        $books = new Books();
        $BooksbyDate = array();
        try {
            $stmt = $books->range($start, $end, 'StartDate');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $p = (object) array(
                    "BookID" => (int) $BookID,
                    "StartDate" => $StartDate,
                    "StartAt" => (int) $StartAt,
                    "CustomerID" => (int) $CustomerID,
                    "ServiceID" => (int) $ServiceID,
                    "Durtion" => (int) $Durtion,
                    "ServiceTypeID" => (int) $ServiceTypeID,
                    "Notes" => $Notes,
                );

                array_push($BooksbyDate, $p);
            }
            if (count($BooksbyDate) == 0)
                throw new Exception("Book not found", 404);
            $hii = range($start, $end);
            BookingService::array_sort_by_column($BooksbyDate, 'StartDate');
            return $BooksbyDate;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Find books by StartDate
     * @return array[Books]
     */
    public static function find_books_by_date($date)
    {
        $books = new Books();
        $BooksbyDate = array();
        try {
            $stmt = $books->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                if ($StartDate == $date) {
                    $p = (object) array(
                        "BookID" => (int) $BookID,
                        "StartDate" => $StartDate,
                        "StartAt" => (int) $StartAt,
                        "CustomerID" => (int) $CustomerID,
                        "ServiceID" => (int) $ServiceID,
                        "Durtion" => (int) $Durtion,
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "Notes" => $Notes,
                    );

                    array_push($BooksbyDate, $p);
                }
            }
            if (count($BooksbyDate) == 0)
                throw new Exception("Book not found", 404);

            BookingService::array_sort_by_column($BooksbyDate, 'StartDate');
            return $BooksbyDate;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Delete book
     * @return bool
     */
    public static function delete_book($id)
    {
        $BooksObj = new Books();
        $BooksObj->BookID = $id;
        if ($BooksObj->delete()->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * get count of all today books
     * @return int
     */
    public static function  get_number_books_today()
    {
        try {
            $BooksDate = date("Y-m-d");
            $todayApps = array();
            $todayApps = BookingService::find_books_by_date($BooksDate);
            return count($todayApps);
        } catch (Exception $e) {
            if ($e->getCode() == 404)
                return 0;
            throw $e;
        }
    }
    /**
     * get count of all week books
     * @return int
     */
    public static function get_number_books_week()
    {
        try {
            $dayofweek = date('w', strtotime(date("Y-m-d")));

            //this check set the sunday first day in week
            if ($dayofweek == 0) {
                $startWeek = date("Y-m-d", strtotime('sunday this week'));
                $endWeek = date("Y-m-d", strtotime('friday next week'));
            } else {
                $startWeek = date("Y-m-d", strtotime('sunday last week'));
                $endWeek = date("Y-m-d", strtotime('friday this week'));
            }
            return count(BookingService::find_books_by_date_range($startWeek, $endWeek));
        } catch (Exception $e) {
            if ($e->getCode() == 404)
                return 0;
            throw $e;
        }
    }

    /**
     *   Fetch All today's appointments and calculate disable slots
     */
    public static function get_slots_exists($Date)
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
        try {
            $AllAppointmentsData = BookingService::find_books_by_date($Date);
        } catch (Exception $e) {
            if ($e->getCode() == 404)
                $AllAppointmentsData = [];
        }

        if ($AllAppointmentsData) {
            foreach ($AllAppointmentsData as $Appointment) {
                $AppStartTimes[] = $Appointment->StartAt;
                $AppEndTimes[] = $Appointment->StartAt + $Appointment->Durtion;

                //now calculate 5min slots between appointments startAt and EndAt
                $start_et = $Appointment->StartAt;
                $end_et = $Appointment->StartAt + $Appointment->Durtion;
                for ($i = $start_et; $i < $end_et; $i += 5) //make 15-10=5min slot
                {
                    // if ($i == $start_et){
                    //    $count = count($AppBetweenTimes)-1;

                    //    if ($count > -1 && $i-5 != $AppBetweenTimes[$count]){
                    //     $EndOfAppTimes[] = $i - 5;
                    //    }
                    // }
                    $AppBetweenTimes[] = $i;
                    // $AppBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');

                    if ($i == $end_et - 5) {
                        $EndOfAppTimes[] = $i + 5;
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

        $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppNextTimes, $LockTimesSlots);
        unset($AppBetweenTimes);
        unset($AppNextTimes);
        unset($LockTimesSlots);
        if (isset($DisableSlotsTimes))
            sort($DisableSlotsTimes);
        return ['DisableSlots' => $DisableSlotsTimes, 'End' => $EndOfAppTimes];
    }

    /**
     *   Fetch All today's appointments and calculate disable slots
     */
    public static function get_slots_exists_for_lock($Date)
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
        try {
            $AllAppointmentsData = BookingService::find_books_by_date($Date);
        } catch (Exception $e) {
            if ($e->getCode() == 404)
                $AllAppointmentsData = [];
        }
        if ($AllAppointmentsData) {
            foreach ($AllAppointmentsData as $Appointment) {
                $AppStartTimes[] = $Appointment->StartAt;
                $AppEndTimes[] = $Appointment->StartAt + $Appointment->Durtion;

                //now calculate 5min slots between appointments startAt and EndAt
                $start_et = $Appointment->StartAt;
                $end_et = $Appointment->StartAt + $Appointment->Durtion;
                for ($i = $start_et; $i < $end_et; $i += 5) //make 15-10=5min slot
                {
                    if ($i == $start_et) {
                        $count = count($AppBetweenTimes) - 1;

                        if ($count > -1 && $i - 5 != $AppBetweenTimes[$count]) {
                            $EndOfAppTimes[] = $i - 5;
                        }
                    }
                    $AppBetweenTimes[] = $i;
                    // $AppBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');

                    if ($i == $end_et - 5) {
                        $EndOfAppTimes[] = $i + 5;
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

        $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppNextTimes, $LockTimesSlots);
        unset($AppBetweenTimes);
        unset($AppNextTimes);
        unset($LockTimesSlots);
        if (isset($DisableSlotsTimes))
            sort($DisableSlotsTimes);
        return ['DisableSlots' => $DisableSlotsTimes, 'End' => $EndOfAppTimes];
    }
    public static function array_sort_by_column(&$array, $column, $direction = SORT_ASC)
    {
        $reference_array = array();

        foreach ($array as $key => $row) {
            $reference_array[$key] = $row->$column;
        }

        array_multisort($reference_array, $direction, $array);
    }

    /**
     * 
     * adding new note to book
     * @return bool
     */
    public static function add_note(Books $book)
    {
        if ($book->AddNotes()->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 
     * return price number for all month by spacfic month
     * @param string $month
     * @param string $year
     * @return int
     */
    public static function get_price_for_book_month($month, $year)
    {
        $first_day_this_month = date($year . '-' . $month . '-01'); // hard-coded '01' for first day
        $last_day_this_month  = date($year . '-' . $month . '-t', strtotime($first_day_this_month));

        $books = new Books();
        try {
            $stmt = $books->get_price_by_month($first_day_this_month, $last_day_this_month);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $p = (object) array(
                    "PriceForAllMonth" => (int) $PriceForAllMonth
                );
            }

            return $p->PriceForAllMonth;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * return price number for current month
     * @return int
     */
    public static function get_price_for_book_thismonth()
    {
        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t');

        $books = new Books();
        try {
            $stmt = $books->get_price_by_month($first_day_this_month, $last_day_this_month);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $p = (object) array(
                    "PriceForAllMonth" => (int) $PriceForAllMonth
                );
            }

            return $p->PriceForAllMonth;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Send Remainder to customer when books tommorow
     */
    public static function send_remainder()
    {
        $books = new Books();
        $stmt = $books->today_books();
        $BooksTodaySend = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $p = (object) array(
                "PhoneNumber" => $PhoneNumber,
                "FirstName" => $FirstName,
                "LastName" => $LastName,
                "ServiceTypeName" => $ServiceTypeName,
                "StartDate" => $StartDate,
                "StartAt" => (int)$StartAt
            );
            array_push($BooksTodaySend, $p);
        }
        foreach ($BooksTodaySend as $key => $value) {
            $Date = strtotime($value->StartDate);
            $NewDate = date("d/m/Y", $Date);
            $decodeText = "היי מירית\nאני מאשר/ת הגעה,\n{$value->FirstName}";
            $encodeText = urlencode($decodeText);

            $LinkWhatApp = "https://wa.me/9720525533979/?text={$encodeText}";
            $link = BookingService::shotlink($LinkWhatApp)->shortUrl;

            $Time = $value->StartAt;
            $newTime = hoursandmins($Time);
            $message = Settings::get_Setting(Settings::SMS_TEMPLATE_REMINDER)['SettingValue'];
            $message = str_replace('\n', PHP_EOL, $message);
            $message = str_replace('{FirstName}', $value->FirstName, $message);
            $message = str_replace('{LastName}', $value->LastName, $message);
            $message = str_replace('{Date}', $NewDate, $message);
            $message = str_replace('{Time}', $newTime, $message);
            $message = str_replace('{ServiceType}', $value->ServiceTypeName, $message);
            $message = str_replace('{Link}', $link, $message);

            $globalSMS = new globalSMS();
            $globalSMS->send_sms($value->PhoneNumber, $message);
        }
        return true;
    }
    static function shotlink($link)
    {
        $arrData = array();
        $arrData['fullName'] = "rebrand.ly";
        $url = 'https://api.rebrandly.com/v1/links';
        $fields = array(
            'domain' => $arrData,
            'destination' => $link
        );
        // Firebase API Key
        $serverKey = 'apikey:895faa9f2e4046ae9f57db94bf8ca9e3';
        $workSpace = 'workspace:20f29af8f1e646b7ad6055f43f50056a';
        $headers = array($serverKey, $workSpace, 'Content-Type:application/json');
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        $PreityResult = json_decode($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $PreityResult;
    }
    /**
     * 
     * @return string $message
     */
    private static function generate_message($book, string $type, $title)
    {
        $regId = UsersService::get_regId_by_userName("mirit");
        // Here, INCLUDE YOUR FCM FILE
        $arrNotification = array();
        $arrData = array();
        $Date = strtotime($book->StartDate);
        $customer = CustomersService::find_customer_by_id($book->CustomerID);
        $ServiceType = ServiceTypesService::get_service_type_by_id($book->ServiceTypeID);

        $NewDate = date("d/m/Y", $Date);
        $newTime = hoursandmins($book->StartAt);

        //generate message updated
        $message = Settings::get_Setting($type)['SettingValue'];
        $message = str_replace('\n', PHP_EOL, $message);
        $message = str_replace('{FirstName}', $customer->FirstName, $message);
        $message = str_replace('{LastName}', $customer->LastName, $message);
        $message = str_replace('{Date}', $NewDate, $message);
        $message = str_replace('{Time}', $newTime, $message);
        $message = str_replace('{ServiceType}', $ServiceType->ServiceTypeName, $message);

        //Generated a FCM Message to android app
        $arrData["StartDate"] = $Date;
        $arrNotification["body"] = $message;
        $arrNotification["title"] = $title;
        $arrNotification["click_action"] = "FCM_PLUGIN_ACTIVITY";
        //send all sms and FCM 
        FCM::send_notification($regId, $arrNotification, $arrData, "Android");
        $decodeToken = $GLOBALS['tokenGlobal'];
        if (!$decodeToken->hasScope(["admin"])) {
            //write to log
            $log = new Logger();
            $log->putLog("$customer->FirstName $customer->LastName change the book to $NewDate and $newTime");
            $globalSMS = new globalSMS();
            $globalSMS->send_sms($customer->PhoneNumber, $message);
        }
    }
}
