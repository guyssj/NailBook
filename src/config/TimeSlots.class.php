<?php


namespace BookNail;

class TimeSlots
{

    public $id;
    public $timeSlot;
    public $disabled;

    public static function RenderSlots($Date,$duration=0)
    {
        $TimeSlots = array();
        $dayofweek = date('w', strtotime($Date));
        $WorkingHours = new WorkingHours();
        
        $WorkingHours->get_hours_by_day($dayofweek);
        $start = $WorkingHours->openTime;

        $end = $WorkingHours->closeTime;
        //$end = strtotime(convertToHoursMins($WorkingHours->closeTime, '%02d:%02d'));
    
    
        for ($i = $start; $i <= $end; $i += 30) {
            $AllSlotTimesList[] = $i;
        }
        //Fetch All today's timeoff and calculate disable slots
       
        $TimeSlotsExist =  BookingService::get_slots_exists($Date);
        $DisableSlotsTimes = $TimeSlotsExist['DisableSlots'];
        $EndOfAppTimes = $TimeSlotsExist['End'];


        foreach ($AllSlotTimesList as $Single) {
            if (!in_array($Single, $DisableSlotsTimes)) {
                $time = new TimeSlots();
                $time->id = $Single;
                $time->timeSlot = convertToHoursMins($Single, '%02d:%02d');
                $time->disabled = false;
                $TimeSlots[] = $time;
                //$TimeSlots[] = ['id' => minutes($Single), 'timeSlot' => $Single];

            }
            // else{
            //     $time = new TimeSlots();
            //     $time->id = $Single;
            //     $time->timeSlot = convertToHoursMins($Single, '%02d:%02d');
            //     $time->disabled = true;
            //     $TimeSlots[] = $time;
            // }
        }

        foreach ($EndOfAppTimes as $Single) {
            if (!in_array($Single, $DisableSlotsTimes)) {
                //$checktime = minutes($Single);
                if ($Single < $WorkingHours->closeTime) {
                    // $TimeSlots[] = ['id' => minutes($Single), 'timeSlot' => $Single];
                    $time = new TimeSlots();
                    $time->id = $Single;
                    $time->timeSlot = convertToHoursMins($Single, '%02d:%02d');
                    $TimeSlots[] = $time;
                }

            }
        }

        foreach ($TimeSlots as $key => $time) {
            $timeTotalnew = $time->id + $duration; //change to duraion
            for ($j = 0; $j < count($DisableSlotsTimes) - 1; $j++) {
                if ($DisableSlotsTimes[$j] > $time->id && $DisableSlotsTimes[$j] < $timeTotalnew) {
                    //filter array
                    unset($TimeSlots[$key]);
                    break;
                }
            }
    
            //change to service
            $WorkDays = new WorkingHours();
            $LockObj = new LockHours();
            $arrayOfTimesLock = $LockObj->get_slots_lock($Date);
            $endTimeOfLockHours = 0;
            if (count($arrayOfTimesLock) > 0) {
                $count = count($arrayOfTimesLock) - 1;
    
                $endTimeOfLockHours = $arrayOfTimesLock[$count] + 5;
            }
            //Check if Lock time is end of close time
            if ($WorkingHours->closeTime <= $endTimeOfLockHours && $timeTotalnew > $endTimeOfLockHours) {
                //filter array
                unset($TimeSlots[$key]);
            }
            $Settings = new Settings();
            $MinAfterClose = 0;
            $MinAfterClose = $Settings->get_Setting('MIN_AFTER_WORK')['SettingValue'];
            //check if close time + 120 bigger from time total of app
    
            if ($WorkingHours->closeTime + $MinAfterClose < $timeTotalnew) {
                //filter array
                unset($TimeSlots[$key]);
            }
        }
        $TimeSlots = array_values($TimeSlots);
        return $TimeSlots;

    }

    public static function RenderSlotsLock($Date)
    {
        $TimeSlots = array();
        $dayofweek = date('w', strtotime($Date));
        $WorkingHours = new WorkingHours();
        $bookExist = new Books();
    
        $WorkingHours->get_hours_by_day($dayofweek);
        $start = $WorkingHours->openTime;

        $end = $WorkingHours->closeTime;
        //$end = strtotime(convertToHoursMins($WorkingHours->closeTime, '%02d:%02d'));
    
    
        for ($i = $start; $i <= $end; $i += 10) {
            $AllSlotTimesList[] = $i;
        }
        //Fetch All today's timeoff and calculate disable slots
       // $TimeSlotsExist = BookingService::get_slots_exists_for_lock($Date);
        $DisableSlotsTimes =array();
        $EndOfAppTimes =array();

        foreach ($AllSlotTimesList as $Single) {
            if (!in_array($Single, $DisableSlotsTimes)) {
                $time = new TimeSlots();
                $time->id = $Single;
                $time->timeSlot = convertToHoursMins($Single, '%02d:%02d');
                $TimeSlots[] = $time;
                //$TimeSlots[] = ['id' => minutes($Single), 'timeSlot' => $Single];

            }
        }

        foreach ($EndOfAppTimes as $Single) {
            if (!in_array($Single, $DisableSlotsTimes)) {
                //$checktime = minutes($Single);
                if ($Single < $WorkingHours->closeTime) {
                    // $TimeSlots[] = ['id' => minutes($Single), 'timeSlot' => $Single];
                    $time = new TimeSlots();
                    $time->id = $Single;
                    $time->timeSlot = convertToHoursMins($Single, '%02d:%02d');
                    $TimeSlots[] = $time;
                }

            }
        }
        return $TimeSlots;

    }
}
