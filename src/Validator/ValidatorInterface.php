<?php

namespace App\Validator;

interface ValidatorInterface
{
    public function validate(mixed $data);
}