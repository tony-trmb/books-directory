<?php

declare(strict_types=1);

namespace App\Command;

final class CreateBookCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $image,
        public array $authors,
        public string $publishDate
    )
    {
    }
}