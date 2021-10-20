<?php

namespace BookNail;

use PDO;
use Exception;
use DateTime;
use DateTimeZone;

class LockHoursService
{

    /**
     * 
     * get all book
     * 
     * @return array[Books]
     */
    public static function get_lockHours()
    {
        $lockHours = new LockHours();
        $LockSHours = array();
        try {
            $stmt = $lockHours->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    //Orignial book from db
                    $Lock = (object) array(
                        "idLockHours" => (int) $idLockHours,
                        "StartDate" => $StartDate,
                        "StartAt" => $StartAt,
                        "EndAt" => $EndAt,
                        "Notes" => $Notes,
                    );

                    //set the time for book
                    $startTime = new DateTime($Lock->StartDate, new DateTimeZone('Asia/Jerusalem'));
                    $startTime->modify("+{$Lock->StartAt} minutes");
                    $endTime = new DateTime($Lock->StartDate, new DateTimeZone('Asia/Jerusalem'));
                    $endTime->modify("+{$Lock->EndAt} minutes");

                    $endTime = $endTime->format('c');
                    $startTime = $startTime->format('c');

                    //object for clendar ionic
                    $p = (object) array(
                        "title" => "זמן נעול",
                        "allDay" => false,
                        "endTime" => $endTime,
                        "startTime" => $startTime,
                        "meta" => null,
                        "LockSlot" => (object)$Lock,
                    );
                    array_push($LockSHours, $p);
                }
            }
            return $LockSHours;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * get hours by date 
     * @return array(\BookNail\LockHours)
     */
    public static function get_hours_by_date($date)
    {
        $lockHours = new LockHours();
        $LockSHours = array();
        try {
            $stmt = $lockHours->get_hours_by_date($date);
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $Lock = (object) array(
                        "idLockHours" => (int) $idLockHours,
                        "StartDate" => $StartDate,
                        "StartAt" => $StartAt,
                        "EndAt" => $EndAt,
                        "Notes" => $Notes,
                    );

                    array_push($LockSHours, $Lock);
                }
            }
            return $LockSHours;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public static function get_slots_lock($Date)
    {
        $EventBetweenTimes = array();
        $LockHoursData = LockHoursService::get_hours_by_date($Date);
        if ($LockHoursData) {
            foreach ($LockHoursData as $LockHour) {
                $start_et = $LockHour->StartAt;
                $end_et = $LockHour->EndAt;
                for ($i = $start_et; $i < $end_et; $i += 5) //making 5min slot
                {
                    // $EventBetweenTimes[] = convertToHoursMins($i, '%02d:%02d');
                    $EventBetweenTimes[] = $i;
                }
            }
        }
        return $EventBetweenTimes;
    }

    public static function add_new_lock_hours(LockHours $lockHour)
    {
        if ($lockHour->add()->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public static function delete_lock_hours($lockHourID)
    {

        $lockHour = new LockHours();
        if ($lockHour->delete($lockHourID)->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
