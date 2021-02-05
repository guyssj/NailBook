<?php

/**
 * 
 * this class for API Response 
 * 
 * Created by Guy Gold. 16/12/2018
 */


namespace BookNail;

class ResultAPI
{
    //Prop
    var $Result;
    var $StatusCode;
    var $ErrorMessage;


    public function __construct($results = null, $statusCode = null, $error = null)
    {
        $this->Result = $results;
        $this->StatusCode = $statusCode;
        $this->ErrorMessage = $error;
    }
    /**
     * 
     * this method set a status code for response code
     * 
     * @param $newReuslt
     */
    function set_result($newResult)
    {
        $this->Result = $newResult;
    }

    /**
     * 
     * this method set a status code for response code
     * 
     */
    function get_result()
    {
        return $this->Result;
    }
    /**
     * 
     * this method set a status code for response code
     * 
     * @param $StatusCode
     */
    function set_statusCode($newStatusCode)
    {
        $this->StatusCode = $newStatusCode;
    }
    /**
     * 
     * this method get a status code for response code
     * 
     */
    function get_statusCode()
    {
        return $this->StatusCode;
    }
    /**
     * 
     * this method set a Message code for response
     * 
     * @param $Errormessage
     */
    function set_ErrorMessage($ErrormessageNew)
    {
        $this->ErrorMessage = $ErrormessageNew;
    }
    /**
     * 
     * this method get a Message code for response
     * 
     */
    function get_ErrorMessage()
    {
        return $this->ErrorMessage;
    }
}
