<?php

declare(strict_types=1);

namespace App\Validator\Exception;

final class InvalidImageException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid image received');
    }
}