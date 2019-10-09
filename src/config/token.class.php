<?php

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
}