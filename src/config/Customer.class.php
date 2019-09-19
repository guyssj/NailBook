<?php
/**
 *
 * this class for Customer
 *
 * Created by Guy Gold. 16/12/2018
 */
class Customer
{

    /**
     * Properites
     */
    public $CustomerID;
    public $FirstName;
    public $LastName;
    public $PhoneNumber;

    public function Add()
    {
        try {
            $sql = "call CustomerSave('$this->FirstName','$this->LastName','$this->PhoneNumber',@l_CustomerID);";
            $db = new db();
            $db = $db->connect2();
            $smst = $db->prepare($sql);
            $smst->bindParam(':FirstName', $this->FirstName);
            $smst->bindParam(':LastName', $this->LastName);
            $smst->bindParam(':PhoneNumber', $this->PhoneNumber);
            $db->query("set character_set_client='utf8'");
            $db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

            $row = $smst->execute();
            $rs2 = $db->query("SELECT @l_CustomerID as id");
            $row2 = $rs2->fetchObject();

            if ($row2->id == 501) {
                $CustomerEx = $this->GetByPhoneNumber($this->PhoneNumber);
                foreach ($CustomerEx as $key => $value) {
                    $this->CustomerID = $value['CustomerID'];
                    return $this->CustomerID;
                }
            }
            return $row2->id;

        } catch (PDOException $e) {
            $var = (string) $e->getMessage();
            return $var;
        }
    }
    public function GetByPhoneNumber($PhoneNumber)
    {
        $sql = "call CustomerGetByPhone('$PhoneNumber');";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            return $row;
        } catch (PDOException $e) {
            //$resultObj->set_ErrorMessage($e->getMessage());
            return json_encode('error', JSON_UNESCAPED_UNICODE);
        }

    }

    public function GetByPhoneNumber2($PhoneNumber)
    {
        $sql = "call CustomerGetByPhone('$PhoneNumber');";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            foreach ($row as $key => $value) {
                if ($value['PhoneNumber'] == $PhoneNumber) {
                    return $value;
                }
            }
            return "Customer not found";
        } catch (PDOException $e) {
            //$resultObj->set_ErrorMessage($e->getMessage());
            return json_encode('error', JSON_UNESCAPED_UNICODE);
        }

    }
    public function GetAllCustomers()
    {
        $sql = "call CustomerGetAll();";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            return $row;
        } catch (PDOException $e) {
            //$resultObj->set_ErrorMessage($e->getMessage());
            return json_encode('error', JSON_UNESCAPED_UNICODE);
        }

    }
    public static function GetCustomerById($ID)
    {
        $sql = "call CustomerGetAll();";
        try {
            $mysqli = new db();
            $mysqli = $mysqli->connect();
            $mysqli->query("set character_set_client='utf8'");
            $mysqli->query("set character_set_results='utf8'");
            $result = $mysqli->query($sql);
            $row = cast_query_results($result);
            foreach ($row as $key => $value) {
                if ($value['CustomerID'] == $ID) {
                    return $value;
                }
            }
            return "Customer not found";
        } catch (PDOException $e) {
            //$resultObj->set_ErrorMessage($e->getMessage());
            return json_encode('error', JSON_UNESCAPED_UNICODE);
        }

    }
    //Cast the Fucking Result
    public function cast_query_results($rs)
    {
        $fields = mysqli_fetch_fields($rs);
        $data = array();
        $types = array();
        foreach ($fields as $field) {
            switch ($field->type) {
                case 3:
                    $types[$field->name] = 'int';
                    break;
                case 4:
                    $types[$field->name] = 'float';
                    break;
                default:
                    $types[$field->name] = 'string';
                    break;
            }
        }
        while ($row = mysqli_fetch_assoc($rs)) {
            array_push($data, $row);
        }

        for ($i = 0; $i < count($data); $i++) {
            foreach ($types as $name => $type) {
                settype($data[$i][$name], $type);
            }
        }
        return $data;
    }
}
