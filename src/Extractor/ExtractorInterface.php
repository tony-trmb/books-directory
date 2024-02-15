<?php

declare(strict_types=1);

namespace App\Extractor;

interface ExtractorInterface
{
    public function extract(mixed $data): string;
}