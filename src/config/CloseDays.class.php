<?php
    /**
     * class for Close Days
     * 
     * Create by Guy Gold 18/11/2019
     * 
     */
    class CloseDays{

        public $CloseDayID;
        public $Date;


    public function get_date_closed()
    {
        $sql = "call CloseDaysGetAll();";
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

    public function add_new_close_date(){
        $sql = "call CloseDaysSet(:Date);";

        try{
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':Date', $this->Date);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute();
            $row = $smst->rowCount();
            if ($row > 0) {
                return true;
            } else {
                return false;
            }
        }catch (Exception $e){
            $var = (string) $e->getMessage();
            return $var;
        }
    }
}