<?php
namespace BookNail;
use mysqli;
use PDO;
    class db{
        //Prop
        private $dbhost;
        private $dbuser;
        private $dbpass;
        private $dbname;
        
        public function __construct() {
            $this->dbhost = $_SERVER['DB_HOST_NAME'];
            $this->dbuser =$_SERVER['DB_USER'];
            $this->dbpass = $_SERVER['DB_PASS'];
            $this->dbname = $_SERVER['DB_NAME'];
        }

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