<?php
namespace BookNail;
use Exception;
class DeviceService
{
  public static function add_regId(Devices $device)
  {
    return $device->add_regId();
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
