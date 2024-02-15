<?php

declare(strict_types=1);

namespace App\Enum;

enum ImageFormat: string
{
    case PNG_FORMAT = 'png';
    case JPEG_FORMAT = 'jpeg';
    case JPG_FORMAT = 'jpg';
}