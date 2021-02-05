<?php


namespace BookNail;

use SoapClient;
use Exception;

class globalSMS
{
    private $un = "guyssj@gmail.com";
    private $pw = "9w92pB";
    private $accid = "4212";
    private $sysPW = "itnewslettrSMS";

    public function send_sms($phones, $message)
    {
        $t = date("Y-m-d H:i:s");
        date_default_timezone_set('Israel');
        try
        {
            $ini = ini_set("soap.wsdl_cache_enabled", "0");
            

            $client = new SoapClient("http://api.itnewsletter.co.il/webServices/WebServiceSMS.asmx?wsdl");

            $params = array();
            $params["un"] = $this->un;
            $params["pw"] = $this->pw;
            $params["accid"] = $this->accid;
            $params["sysPW"] = $this->sysPW;
            $params["t"] = $t;

            $params["txtUserCellular"] = "Miritush";
            $params["destination"] = $phones;
            $params["txtSMSmessage"] = $message;
            $params["dteToDeliver"] = "";
            $params["txtAddInf"] = "LocalMessageID";

            $result = $client->sendSmsToRecipients($params)->sendSmsToRecipientsResult;

            return $result;
        } catch (Exception $e) {
            return true;
        }
    }
}
