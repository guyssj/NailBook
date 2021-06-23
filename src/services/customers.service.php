<?php

namespace BookNail;

use Exception;
use PDO;

class CustomersService
{
    /**
     * 
     * get all book
     * 
     * @return int Customer ID
     */
    public static function add_customer(Customer $customer)
    {
        try {
            $cusId = $customer->add();
            if ($cusId > 0) {
                if ($cusId == 501) {
                    $CustomerEx = CustomersService::find_customer_id_by_phone($customer->PhoneNumber);
                    return $CustomerEx;
                }
                return $cusId;
            } else {
                throw new ConflictException($cusId);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * 
     * get all customers Sort by first name
     * 
     * @return array[Customer]
     */
    public static function get_customers()
    {
        $customer = new Customer();
        $Customers = array();
        try {
            $stmt = $customer->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "CustomerID" => (int) $CustomerID,
                        "FirstName" => $FirstName,
                        "LastName" => $LastName,
                        "PhoneNumber" => $PhoneNumber,
                        "Color" => $Color,
                        "Notes" => $Notes
                    );

                    array_push($Customers, $p);
                }
            }
            BookingService::array_sort_by_column($Customers, 'FirstName', SORT_ASC);
            return $Customers;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function update_customer(Customer $customer)
    {
        try {
            if ($customer->update()->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function find_customer_id_by_phone($phoneNumber)
    {
        $customers = CustomersService::get_customers();

        foreach ($customers as $customer) {
            if ($customer->PhoneNumber == $phoneNumber)
                return $customer->CustomerID;
        }
        throw new Exception("Customer not found", 404);
    }

    public static function find_customer_by_phone($phoneNumber)
    {
        $customers = CustomersService::get_customers();

        foreach ($customers as $customer) {
            if ($customer->PhoneNumber == $phoneNumber)
                return $customer;
        }
        throw new Exception("Customer not found", 404);
    }

    public static function find_customer_by_id($ID)
    {
        $customers = CustomersService::get_customers();

        foreach ($customers as $customer) {
            if ($customer->CustomerID == $ID)
                return (object)$customer;
        }
        throw new Exception("Customer not found", 404);
    }
}
