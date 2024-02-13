<?php

namespace App\Validator;

use App\Enum\ImageFormat;

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
            throw new \Exception();
        }

        if (
            !\str_contains($fileInfo, ImageFormat::PNG_FORMAT->value)
            && \str_contains($fileInfo, ImageFormat::JPG_FORMAT->value)
        ) {
            throw new \Exception();
        }

        $padding = match (true) {
            \str_ends_with($data, '==') => 2,
            \str_ends_with($data, '=') => 1,
            default => 0
        };
        $imageSize = (float) (\strlen($data) * (3/4)) - $padding;

        if ($imageSize > self::MAX_IMAGE_SIZE) {
            throw new \Exception();
        }
    }
}