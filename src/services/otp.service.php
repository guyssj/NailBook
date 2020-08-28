<?php
use Firebase\JWT\JWT;

class OTPService{
    public static function verfiy_otp($otp,$customerId){
        //$customer = CustomersService::find_customer_by_id($customerId);
        $otpClass = new OTP();
        $now = new DateTime();
        $future = new DateTime("now +10 minute");
        try {
            $stmt = $otpClass->read($customerId);
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ((int) $OTP == $otp) {
                        $payload = [
                            "iat" => $now->getTimeStamp(),
                            "exp" => $future->getTimeStamp(),
                            "sub" => $otp,
                        ];
                        $token = JWT::encode($payload, $_SERVER['Secret'], "HS256");
                        return true;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function verfiy_reset_otp($otp,$customerId){
        $otpClass = new OTP();
        $now = new DateTime();
        $future = new DateTime("now +10 minute");
        try {
            $stmt = $otpClass->read($customerId);
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ((int) $OTP == $otp) {
                        $payload = [
                            "iat" => $now->getTimeStamp(),
                            "exp" => $future->getTimeStamp(),
                            "sub" => $otp,
                        ];
                        $token = JWT::encode($payload, $_SERVER['Secret'], "HS256");
                        $otpClass->add($customerId);
                        return true;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function add_otp($customerId){
        $otpClass = new OTP();
        $customer = CustomersService::find_customer_by_id($customerId);
        $otp = $otpClass->add($customerId);
        if($otp > 0){
            $globalSMS = new globalSMS();
            $message = "קוד אימות: $otp";
            $globalSMS->send_sms($customer->PhoneNumber, $message);

            return true;
        }
        else
            return false;
    }
}

?>