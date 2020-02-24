<?php
    /**
     * class for Holidays
     * 
     * Create by Guy Gold 21/02/2020
     * 
     */
    class Holidays{

        public $HolidayID;
        public $Date;
        public $Notes;


        public function get_holidays()
        {
            $sql = "SELECT * FROM Holidays WHERE Date > NOW() ORDER BY Date ASC";
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


    }