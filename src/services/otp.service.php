<?php
use Firebase\JWT\JWT;

class OTPService{
    public static function verfiy_otp($otp,$customerPhoneNumber){
        $otpClass = new OTP();
        $now = new DateTime();
        $future = new DateTime("now +10 minute");
        try {
            $stmt = $otpClass->read($customerPhoneNumber);
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ((int) $OTP == $otp) {
                        $payload = [
                            "iat" => $now->getTimeStamp(),
                            "exp" => $future->getTimeStamp(),
                            "sub" => $otp,
                            "auth" => $customerPhoneNumber,
                            "scope" => ["read"]
                        ];
                        $token = JWT::encode($payload, $_SERVER['Secret'], "HS256");
                        $user = new Users();
                        $user->userName = $customerPhoneNumber;
                        $user->token = $token;
                        $otpClass->add($customerPhoneNumber);
                        return $user;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * 
     * add OTP to customer with PhoneNumber
     */
    public static function add_otp($customerPhoneNumber){
        $otpClass = new OTP();
        $otp = $otpClass->add($customerPhoneNumber);
        if($otp > 0){
            $globalSMS = new globalSMS();
            $message = "$otp זה קוד האימות שלך";
            $globalSMS->send_sms($customerPhoneNumber, $message);
            return true;
        }
        else
            return false;
    }

    /**
     * Verfiy OTP and token
     */
    public static function verfiy_token($token){
        $Test = str_replace("Bearer", "", $token[0]);
        $decode = JWT::decode(ltrim($Test, " "), $_SERVER['Secret'], ["HS256"]);
        $phoneNumber = $decode->auth;
        $now = new DateTime();
        if ($decode->exp >= $now->getTimeStamp())
            return $phoneNumber;
        return false;
    }
}

?>