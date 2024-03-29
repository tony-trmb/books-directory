<?php

declare(strict_types=1);

namespace App\Validator;

interface ValidatorInterface
{
    public function validate(mixed $data);
}