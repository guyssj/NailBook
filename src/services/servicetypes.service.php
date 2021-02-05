<?php
namespace BookNail;

use PDO;
use Exception;

class ServiceTypesService{

    /**
     * get all service types
     *  @return array[ServiceTypes]
     */
    public static function get_service_types()
    {
        $ServiceTypesObj = new ServiceTypes();
        $ServiceTypes = array();
        try {
            $stmt = $ServiceTypesObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "ServiceTypeName" => $ServiceTypeName,
                        "ServiceID" => (int) $ServiceID,
                        "Duration" => (int) $Duration,
                        "Price" => $Price,
                        "Description" => $Description,
                    );

                    array_push($ServiceTypes, $p);
                }
            }
            return $ServiceTypes;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * find service type by service id
     * 
     * @param int $ServiceId
     * 
     * @return array[ServiceType]
     */
    public static function find_service_type_by_service($sID)
    {
        $ServiceTypesObj = new ServiceTypes();
        $ServiceTypesBySID = array();
        try {
            $stmt = $ServiceTypesObj->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                if ((int) $ServiceID == (int) $sID) {
                    $p = (object) array(
                        "ServiceTypeID" => (int) $ServiceTypeID,
                        "ServiceTypeName" => $ServiceTypeName,
                        "ServiceID" => (int) $ServiceID,
                        "Duration" => (int) $Duration,
                        "Price" => $Price,
                        "Description" => $Description,
                    );

                    array_push($ServiceTypesBySID, $p);
                }
            }
            return $ServiceTypesBySID;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function add_service_type(ServiceTypes $ServiceType){
        try{
            if($ServiceType->add() > 0){
                return true;
            }
            return false;
            
        }
        catch(Exception $e){
            throw $e;
        }
    }

    public static function get_service_type_by_id($ID)
    {
        $ServiceTypesObj = new ServiceTypes();
        try {
            $stmt = $ServiceTypesObj->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ((int) $ServiceTypeID == $ID) {
                        $ServiceType = (object) array(
                            "ServiceTypeID" => (int) $ServiceTypeID,
                            "ServiceTypeName" => $ServiceTypeName,
                            "ServiceID" => (int) $ServiceID,
                            "Duration" => (int) $Duration,
                            "Price" => $Price,
                            "Description" => $Description,
                        );

                        return $ServiceType;
                    }
                }
            }
            return $ServiceTypes;
        } catch (Exception $e) {
            throw $e;
        }
    }
}