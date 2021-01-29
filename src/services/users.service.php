<?php

class UsersService
{
    public static function get_users()
    {
        $user = new Users();
        $Users = array();
        try {
            $stmt = $user->read();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $p = (object) array(
                        "id" => (int) $id,
                        "Password" => $Password,
                        "UserName" => $UserName,
                        "RegId" => $RegId
                    );

                    array_push($Users, $p);
                }
            }
            BookingService::array_sort_by_column($Users, 'UserName', SORT_ASC);
            return $Users;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
