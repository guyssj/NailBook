<?php

namespace BookNail;


class Token
{
    private $decoded;
    public function __construct(array $decoded)
    {
        $this->populate($decoded);
    }
    public function populate(array $decoded)
    {
        $this->decoded = $decoded;
    }
    public function hasScope(array $scope)
    {
        return !!count(array_intersect($scope, $this->decoded["scope"]));
    }
    public function getUser():Users
    {
        $user = new Users();
        if ($this->decoded["sub"]) {
            $dalUser = $this->decoded["sub"];
            $user->userName = $dalUser->UserName;
            $user->password = $dalUser->Password;
            $user->regId = $dalUser->RegId;
            $user->id = $dalUser->id;
        }
        return $user;
    }
    public function getAuth()
    {
        if ($this->decoded["auth"])
            return $this->decoded["auth"];
    }
}
