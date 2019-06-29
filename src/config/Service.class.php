<?php
    /**
     * 
     * Service class 
     * 
     * Created by guy gold 24/6/2019
     */
    class Services{

        private $ServiceID;
        private $ServiceName;


        //Methods

        /**
         * 
         * Get all service from database
         */
        Public function GetAllServices(){
            $sql = "call ServiceGetAll();";
            try{
               $mysqli = new db();
               $mysqli = $mysqli->connect();
               $mysqli->query("set character_set_client='utf8'");
               $mysqli->query("set character_set_results='utf8'");
               $result = $mysqli->query($sql);
               $row = cast_query_results($result);
               return $row;
            }catch(PDOException $e){
                $var = (string)$e->getMessage();
                return $var;        
            }
        }
    }