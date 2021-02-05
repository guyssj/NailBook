<?php
namespace BookNail;

use PDO;
use Exception;
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
    public static function del_close_day($id){
        $closeDay = new CloseDays();
        $closeDay->CloseDayID = $id;
        if ($closeDay->delete() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
