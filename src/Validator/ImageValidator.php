<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enum\ImageFormat;
use App\Validator\Exception\InvalidImageException;
use App\Validator\Exception\InvalidImageFormatException;
use App\Validator\Exception\InvalidImageSizeException;

final class ImageValidator implements ValidatorInterface
{
    private const MAX_IMAGE_SIZE = 2000000;
    public function validate(mixed $data): void
    {
        $fileInfo = \explode( ',', $data)[0] ?? null;

        if (
            !\is_string($data)
            || null === $fileInfo
            || $fileInfo === $data
        ) {
            throw new InvalidImageException();
        }

        if (
            !\str_contains($fileInfo, \sprintf('/%s', ImageFormat::PNG_FORMAT->value))
            && !\str_contains($fileInfo, \sprintf('/%s', ImageFormat::JPEG_FORMAT->value))
            && !\str_contains($fileInfo, \sprintf('/%s', ImageFormat::JPG_FORMAT->value))
        ) {
            throw new InvalidImageFormatException();
        }

        $padding = match (true) {
            \str_ends_with($data, '==') => 2,
            \str_ends_with($data, '=') => 1,
            default => 0
        };
        $imageSize = (float) (\strlen($data) * (3/4)) - $padding;

        if ($imageSize > self::MAX_IMAGE_SIZE) {
            throw new InvalidImageSizeException();
        }
    }
}