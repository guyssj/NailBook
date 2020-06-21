<?php
class TimeSlots
{

    public $id;
    public $timeSlot;
    public $disabled;

    public static function RenderSlots($Date)
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
    
    
        for ($i = $start; $i <= $end; $i += 30) {
            $AllSlotTimesList[] = $i;
        }
        //Fetch All today's timeoff and calculate disable slots
        $TimeSlotsExist = BookingService::get_slots_exists_for_lock($Date);
        $DisableSlotsTimes = $TimeSlotsExist['DisableSlots'];
        $EndOfAppTimes = $TimeSlotsExist['End'];

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
