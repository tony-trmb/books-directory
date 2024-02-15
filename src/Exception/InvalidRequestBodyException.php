<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidRequestBodyException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid request body');
    }
}