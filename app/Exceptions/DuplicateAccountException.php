<?php

namespace App\Exceptions;

use Exception;

class DuplicateAccountException extends Exception
{
    protected $code = 409;
}