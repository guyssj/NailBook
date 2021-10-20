<?php

namespace BookNail;

use Exception;
use PDO;

class WorkingHoursService
{
    public static function get_all_hours()
    {
        $workingObj = new WorkingHours();
        $WorkingHoursList = array();
        try {
            $stmt = $workingObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "DayOfWeek" => (int) $DayOfWeek,
                        "OpenTime" => $OpenTime,
                        "CloseTime" => $CloseTime,
                    );

                    array_push($WorkingHoursList, $p);
                }
            }
            return $WorkingHoursList;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public static function get_hours_by_day($dayOfWeek)
    {
        $workingObj = new WorkingHours();
        try {
            $stmt = $workingObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ((int) $DayOfWeek == (int) $dayOfWeek) {
                        $workobj = (object) array(
                            "dayOfWeek" => (int) $DayOfWeek,
                            "openTime" => (int)$OpenTime,
                            "closeTime" => (int)$CloseTime,
                        );
                        return $workobj;
                    }
                }
            }
            return null;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
