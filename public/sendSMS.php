<?php

$un = "guyssj@gmail.com";
$pw = "9w92pB";
$accid = "4212";
$sysPW = "itnewslettrSMS";
$t = date("Y-m-d H:i:s");

/* ---------------------  */
/* send SMS to recipients */
/* ---------------------  */
try
{
	$ini = ini_set("soap.wsdl_cache_enabled","0");

	$client = new SoapClient("http://api.itnewsletter.co.il/webServices/WebServiceSMS.asmx?wsdl");

	$params = array();
	$params["un"] = $un;
	$params["pw"] = $pw;
	$params["accid"] = $accid;
	$params["sysPW"] = $sysPW;
	$params["t"] = $t;

	$params["txtUserCellular"] = "Miritush";
	$params["destination"] = "0504277550,0546626401";
	$params["txtSMSmessage"] = "בדיקה - test";
	$params["dteToDeliver"] = "";
	$params["txtAddInf"] = "LocalMessageID";
	
	
	$result = $client->sendSmsToRecipients($params)->sendSmsToRecipientsResult;

	echo $result;
}

catch (Exception $e)  
{
	echo $e;
}

?>
