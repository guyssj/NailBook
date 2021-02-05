<?php

namespace BookNail;
use Firebase\JWT\JWT;

use PDO;
use Exception;
use DateTime;

class OTPService{
    public static function verfiy_otp($otp,$customerPhoneNumber){
        $otpClass = new OTP();
        $now = new DateTime();
        $future = new DateTime("now +10 minute");
        $customer = CustomersService::find_customer_by_phone($customerPhoneNumber);
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
                            "auth" => $customer,
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
        try {
            $decode = JWT::decode(ltrim($Test, " "), $_SERVER['Secret'], ["HS256"]);
            $phoneNumber = new Token([
                "iat" => $decode->iat,
                "exp" => $decode->exp,
                "sub" => $decode->sub,
                "auth" => $decode->auth,
                "scope" => $decode->scope
            ]);    
        } catch (\Throwable $th) {
            $decode = JWT::decode(ltrim($Test, " "), $_SERVER['Secret'], ["HS384"]);
            $phoneNumber = new Token([
                "iat" => $decode->iat,
                "exp" => $decode->exp,
                "sub" => $decode->sub,
                "auth" => null,
                "scope" => $decode->scope
            ]);            }
        $now = new DateTime();
        if ($decode->exp >= $now->getTimeStamp())
            return $phoneNumber;
        return false;
    }
}
