<?php

namespace BookNail;

use PDO;
use Exception;

class HolidayService
{


    /**
     * 
     * get all holidays from now
     * 
     * @return array
     */
    public static function get_holidays()
    {
        $holiday = new Holidays();
        $Holidays = array();
        try {
            $stmt = $holiday->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "HolidayID" => (int) $HolidayID,
                        "Date" => $Date,
                        "Notes" => $Notes,
                    );

                    array_push($Holidays, $p);
                }
            }
            return $Holidays;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
