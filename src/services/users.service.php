<?php
namespace BookNail;

use PDO;
use Exception;
use DateTime;
use Firebase\JWT\JWT;

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

    public static function sign_in(Users $user){

        $now = new DateTime();
        $future = new DateTime("now +2 hours");
    
        $auth = $user->sign_in();
    
        if(!$auth) throw new Exception("User name or password do not match our records",500);

        session_start();
    
        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "sub" => $auth,
            "scope" => ["admin"]
        ];
        $token = JWT::encode($payload, $_SERVER['Secret'], "HS384");
        $_SESSION['TokenApi'] = $token;
    
        $user->token = $token;
        $user->password = "";

        return $user;
    }

    public static function add_regId(Users $user)
    {
      return $user->add_regId();
    }
  
    public static function get_regId_by_userName($userName)
    {
      $users = UsersService::get_users();
  
      foreach ($users as $user) {
        if ($user->UserName == $userName)
          return $user->RegId;
      }
      throw new Exception("User not found", 404);
    }
}
