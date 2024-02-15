<?php

declare(strict_types=1);

namespace App\Extractor;

use App\Enum\ImageFormat;
use App\Extractor\Dto\ImageExtractorDto;

final class ImageExtractor implements ExtractorInterface
{
    /**
     * @param ImageExtractorDto $data
     */
    public function extract(mixed $data): string
    {
        $path = \sprintf(__DIR__ . '/../Images/%s/%s', $data->getBookName(), $data->getImageName());

        try {
            $type = ImageFormat::PNG_FORMAT->value;
            $data = \file_get_contents(\sprintf('%s.%s', $path, $type));
        } catch (\Throwable) {
            try {
                $type = ImageFormat::JPG_FORMAT->value;
                $data = \file_get_contents(\sprintf('%s.%s', $path, $type));
            } catch (\Throwable) {
                try {
                    $type = ImageFormat::JPEG_FORMAT->value;
                    $data = \file_get_contents(\sprintf('%s.%s', $path, $type));
                } catch (\Throwable) {
                    return '';
                }
            }
        }

        return \sprintf('data:image/%s;base64,%s', $type, \base64_encode($data));
    }
}