<?php

declare(strict_types=1);

namespace App\Validator\Exception;

final class InvalidImageSizeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Image with unsupported size received');
    }
}