<?php

    require "../src/config/db.php";
    require "../src/config/globalSMS.class.php";
    require "../src/config/Settings.class.php";
    $sql = "call TodayBooks();";
    
    try {
        $mysqli = new db();
        $mysqli = $mysqli->connect();
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set character_set_results='utf8'");
        $result = $mysqli->query($sql);
        $row = cast_query_results($result);
        $globalSMS = new globalSMS();

        foreach ($row as $key => $value) {
            $phone = $value['PhoneNumber'];
            $FirstName = $value['FirstName'];
            $LastName = $value['LastName'];
            $ServiceType = $value['ServiceTypeName'];
            $Date = strtotime($value['StartDate']);
            $NewDate = date("d/m/Y",$Date);
            $decodeText = "היי מירית\nאני מאשר/ת הגעה,\n{$FirstName}";
            $encodeText =urlencode($decodeText);
            $LinkWhatApp = "https://wa.me/9720525533979/?text={$encodeText}";
            $Time = $value['StartAt'];
            $newTime = hoursandmins($Time);
            $message = Settings::get_Setting(Settings::SMS_TEMPLATE_REMINDER)['SettingValue'];
            $message = str_replace('\n',PHP_EOL,$message);
            $message = str_replace('{FirstName}',$FirstName,$message);
            $message = str_replace('{LastName}',$LastName,$message);
            $message = str_replace('{Date}',$NewDate,$message);
            $message = str_replace('{Time}',$newTime,$message);
            $message = str_replace('{ServiceType}',$ServiceType,$message);
            $message = str_replace('{Link}',$LinkWhatApp,$message);

           // $message ="שלום {$FirstName} {$LastName} ,\nזאת תזכורת לטיפול {$ServiceType} אצל מיריתוש\nבתאריך {$NewDate} בשעה {$newTime}\nלא לשכוח מסכה\n\nלאישור הגעה לחצ/י על הלינק: {$LinkWhatApp}";
            //$message = "שלום {$FirstName} {$LastName} , הנך מוזמן לפגישה ל {$ServiceType}";
            $globalSMS->send_sms($phone,$message);

        }
        echo json_encode($row, JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        return $e->message();
    }
    /**
 * Casting result with current type and not only string
 */
function cast_query_results($rs)
{
    $fields = mysqli_fetch_fields($rs);
    $data = array();
    $types = array();
    foreach ($fields as $field) {
        switch ($field->type) {
            case 3:
                $types[$field->name] = 'int';
                break;
            case 4:
                $types[$field->name] = 'float';
                break;
            default:
                $types[$field->name] = 'string';
                break;
        }
    }
    while ($row = mysqli_fetch_assoc($rs)) {
        array_push($data, $row);
    }

    for ($i = 0; $i < count($data); $i++) {
        foreach ($types as $orgname => $type) {
            settype($data[$i][$orgname], $type);
        }
    }
    return $data;
}

function hoursandmins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

?>