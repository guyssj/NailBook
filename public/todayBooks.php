<?php

    require "../src/config/db.php";

    $Date = strtotime("tomorrow");
    $Date = date("Y-m-d", $Date);
    $sql = "SELECT * FROM Books WHERE StartDate='$Date';";
    try {
        $mysqli = new db();
        $mysqli = $mysqli->connect();
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set character_set_results='utf8'");
        $result = $mysqli->query($sql);
        $row = cast_query_results($result);

        echo json_encode($row, JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        return $e->message();
    }


    /**
 * Casting result with current type and not only string
 */
function cast_query_results($rs)
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
        foreach ($types as $orgname => $type) {
            settype($data[$i][$orgname], $type);
        }
    }
    return $data;
}

?>