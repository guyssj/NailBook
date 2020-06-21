<?php

class ServicesService{
        /**
     * get all service s
     *  @return array[Services]
     */
    public static function get_services()
    {
        $ServicesObj = new Services();
        $Services = array();
        try {
            $stmt = $ServicesObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "ServiceID" => (int) $ServiceID,
                        "ServiceName" => $ServiceName,
                    );

                    array_push($Services, $p);
                }
            }
            return $Services;
        } catch (Exception $e) {
            throw $e;
        }
    }
}