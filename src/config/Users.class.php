<?php

/**
 * 
 * this class for Users
 * 
 * Created by Guy Gold. 16/12/2018
 */
class Users
{

    //Connection
    private $connection;
    private $dbclass;

    public $userName;
    public $password;
    public $token;


    public function from_array($array)
    {
        foreach (get_object_vars($this) as $attrName => $attrValue) {
            if (isset($array[$attrName]))
                $this->{$attrName} = $array[$attrName];
        }
    }
    public function connectDB()
    {
        $this->dbclass = new db();
        $this->connection = $this->dbclass->connect2();
    }

    public function sign_in()
    {
        $sql = "call SignIn('$this->userName','$this->password');";
        //$sql = "SELECT * FROM APIKeys WHERE APIKey = '$this->password' AND UserName = '$this->userName' LIMIT 1";
        $mysqli = new db();
        $mysqli = $mysqli->connect();
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set character_set_results='utf8'");
        $result = $mysqli->query($sql);
        $row = cast_query_results($result);
        foreach ($row as $key => $value) {
            if (password_verify($this->password, $value['Password'])) {
                return $value;
            } else {
                return false;
            }
        }
    }
        /**
     * 
     * read all customers from db
     * 
     * 
     */
    public function read()
    {

        try {
            $this->connectDB();
            $sqlquery = "SELECT * FROM Users";
            $stmt = $this->connection->prepare($sqlquery);
            $this->connection->query("set character_set_client='utf8'");
            $this->connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $stmt->execute();
            $this->connection = null;
            return $stmt;
        } catch (PDOException $e) {
            throw $e->getMessage();
        }
    }

    public function create_new_user()
    {
        $sql = "call UserSet(:UserName,:Password,@l_Userid);";
        $options = ['cost' => 12];
        try {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT, $options);
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':UserName', $this->userName);
            $smst->bindParam(':Password', $this->password);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
            $row = $smst->execute(['UserName' => $this->userName, 'Password' => $this->password]);
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
}
