<?php
    class db{
        //Prop
        private $dbhost = 'localhost';
        private $dbuser = 'root';
        private $dbpass = 'guygoldi';
        private $dbname = 'reptouch_booknail';


        //Connect to Database (mySQL)

        public function connect(){
            $mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
                /* check connection */
             if ($mysqli->connect_errno) {
                 //printf("Connect failed: %s\n", $mysqli->connect_error);
                 echo json_encode( '{"error":{"text": '.$mysqli->connect_error.'}}');
                 exit();
            }
            return $mysqli;
        }
        public function connect2(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            //$dbConnection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            //$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }


    }