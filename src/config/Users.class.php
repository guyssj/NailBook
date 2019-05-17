<?php 
    /**
     * 
     * this class for Users
     * 
     * Created by Guy Gold. 16/12/2018
     */
    class Users{

        Public $userName;
        Public $key;


        public function checkAPIKey(){
            $sql = "SELECT * FROM APIKeys WHERE APIKey = '$this->key' AND UserName = '$this->userName' LIMIT 1";
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $rowcount = mysqli_num_rows($result);
            if ($rowcount == 1){
                return true;
            }
            return false;
        }
    }