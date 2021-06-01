<?php

namespace BookNail;

use PDO;
use Exception;
use stdClass;

class CalendarService
{

    /**
     * 
     * get all CloseDays from now
     * 
     * @return array[CloseDays]
     */
    public static function get_date_closed()
    {
        $closeDay = new CloseDays();
        $CloseDays = array();
        try {
            $stmt = $closeDay->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "CloseDaysID" => (int) $CloseDaysID,
                        "Date" => $Date,
                        "Notes" => $Notes,
                    );

                    array_push($CloseDays, $p);
                }
            }
            return $CloseDays;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * get all CloseDays and holidays from now
     * 
     * @return array[CloseDays,Holidays]
     */
    public static function get_holiday_and_closed()
    {
        $closeDays = CalendarService::get_date_closed();
        $holiday = HolidayService::get_holidays();

        return array_merge($closeDays, $holiday);
    }

    /**
     * 
     * get all CloseDays and holidays from now
     * 
     * @return array[CloseDays,Holidays]
     */
    public static function refresh_holiday()
    {
        $url = 'https://www.hebcal.com/hebcal?v=1&cfg=json&maj=on&min=on&mod=off&nx=off&year=now&month=x&ss=off&mf=off&c=off&geo=geoname&geonameid=3448439&s=off';
        $headers = array('Content-Type:application/json');
        // Open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $PreityResult = json_decode($result);
        $holidays = HolidayService::get_holidays();

        $newAr =  array_values(array_filter($PreityResult->items, function ($value, $key) {
            $date = strtotime($value->date) > strtotime(date("Y/m/d"));
            $NewDate = date("d/m/Y", strtotime($value->date));
            if (isset($value->yomtov) && $date)
                if ($value->yomtov)
                    return true;
                else return false;
            else return false;
        }, ARRAY_FILTER_USE_BOTH));
        foreach ($newAr as $key => $value) {
            $existDate = false;
            foreach ($holidays as $key2 => $value2) {
                if ($value->date == $value2->Date) {
                    $existDate = true;
                    break;
                }
            }
            if (!$existDate) {
                $holiday = new Holidays();
                $holiday->Date = $value->date;
                $holiday->Notes = $value->title;
                $holiday->add();
            }
        }
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    /**
     * add a new close day
     */
    public static function add_new_close_day(CloseDays $closeDay)
    {
        try {
            if ($closeDay->add() > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * delete close day by id
     */
    public static function del_close_day($id)
    {
        $closeDay = new CloseDays();
        $closeDay->CloseDayID = $id;
        if ($closeDay->delete() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
