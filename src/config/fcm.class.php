<?php


namespace BookNail;


class FCM
{
    function __construct()
    {
    }
    /*
For Sending Push Notification dsdsd
*/
    public static function send_notification($registatoin_ids, $notification, $dataParams, $device_type)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        if ($device_type == "Android") {
            $fields = array(
                'notification' => $notification,
                'data' => $dataParams,
                'to' => $registatoin_ids,
                'priority' => 'high',

            );
        } else {
            $fields = array(
                'to' => $registatoin_ids,
                'notification' => $notification
            );
        }
        // Firebase API Key
        $serverKey = 'Authorization:key=AAAAGPVbugc:APA91bGat7BkLCzZ5qjt9YuLn1D71D9T4KAjtqzSOQUjmJxu3cSXoe3trwn8cPHdKqauRllsBJ8o1aDBMZTmJydIdGeG-BhYfTnont6Kjr4a95Y2zSkyYGADyxwAjWuC_4R2xbcnkmmf';
        $headers = array($serverKey, 'Content-Type:application/json');
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
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }
}
