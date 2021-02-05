<?php

/**
 * class for Settings
 * 
 * Create by Guy Gold 29/04/2020
 * 
 */


namespace BookNail;

use PDOException;
use Exception;
use PDO;

class Settings
{

    public $SettingName;
    public $SettingValue;
    public const SEND_SMS_APP = "SEND_SMS_APP";
    public const MIN_AFTER_WORK = "MIN_AFTER_WORK";
    public const SMS_TEMPLATE_APP = "SMS_TEMPLATE_APP";
    public const SMS_TEMPLATE_REMINDER = "SMS_TEMPLATE_REMINDER";

    public function from_array($array)
    {
        foreach (get_object_vars($this) as $attrName => $attrValue)
            $this->{$attrName} = $array[$attrName];
    }

    public static function get_Setting($settingName)
    {

        $sql = "call SettingGet(:SettingName);";
        try {
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':SettingName', $settingName);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute();
            $ro2 = $smst->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ro2 as $value) {
                return $value;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function get_All_Settings()
    {
        $sql = "SELECT * FROM Settings";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            return $row;
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
    public function set_Setting()
    {

        $sql = "call SettingSet(:SettingName,:SettingValue);";
        try {
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':SettingName', $this->SettingName);
            $smst->bindParam(':SettingValue', $this->SettingValue);

            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute();
            $count = $smst->rowCount();
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }

    public function update_setting()
    {
        $sql = "call SettingUpdate(:SettingName,:SettingValue);";
        $sql2 = "SELECT * FROM Settings WHERE SettingName='$this->SettingName' LIMIT 1;";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql2);
            $rowcount = mysqli_num_rows($result);
            if ($rowcount <= 0) {
                // $result->close();

                throw new Exception("settingsNotUpdate");
            } else {

                $db = new db();
                $db = $db->connect2();
                $smst = $db->prepare($sql);
                $smst->bindParam(':SettingName', $this->SettingName);
                $smst->bindParam(':SettingValue', $this->SettingValue);

                $db->query("set character_set_client='utf8'");
                $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
                $row = $smst->execute();
                $row = $smst->rowCount();
                $ro2 = $smst->fetchAll(PDO::FETCH_ASSOC);
                //$ro2 = cast_query_results($ro2);
                foreach ($ro2 as $value) {
                    return $value;
                }
            }
        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
}



/**
 * @method static self SEND_SMS_APP()
 * @method static self MIN_AFTER_WORK()
 * @method static self SMS_TEMPLATE_APP()
 * @method static self SMS_TEMPLATE_REMINDER()
 */
class SettingName
{
}
