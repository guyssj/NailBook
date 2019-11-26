<?php

class Logger
{

    private $file;
    private $timestamp;

    public function __construct()
    {
        $this->setTimestamp();
        $this->file = 'logsFile';
    }

    public function setTimestamp()
    {
        $this->timestamp = date("D M d 'y h.i A") . " &raquo; ";
    }

    public function putLog($insert)
    {
        if (isset($this->timestamp)) {
            file_put_contents($this->file, $this->timestamp . $insert . "<br>", FILE_APPEND);
        } else {
            trigger_error("Timestamp not set", E_USER_ERROR);
        }
    }

    public function getLog()
    {
        $content = @file_get_contents($this->file);
        return $content;
    }

}
