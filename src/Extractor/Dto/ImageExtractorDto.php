<?php

declare(strict_types=1);

namespace App\Extractor\Dto;

final class ImageExtractorDto
{
    public function __construct(private readonly string $imageName, private readonly string $bookName)
    {
    }

    public static function create(string $imageName, string $bookName): self
    {
        return new self($imageName, $bookName);
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function getBookName(): string
    {
        return $this->bookName;
    }
}