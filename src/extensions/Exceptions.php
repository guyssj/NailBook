<?php

namespace BookNail;

use Exception;
use Throwable;

class NotFoundException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, 404, null);
    }
}
class UnauthorizedException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, 401, null);
    }
}
class ForbiddenException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, 403, null);
    }
}
class ConflictException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, 409, null);
    }
}
class InternalServerErrorException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, 500, null);
    }
}
