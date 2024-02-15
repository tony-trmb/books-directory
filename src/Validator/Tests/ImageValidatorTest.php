<?php

declare(strict_types=1);

namespace App\Validator\Tests;

use App\Validator\Exception\InvalidImageException;
use App\Validator\Exception\InvalidImageFormatException;
use App\Validator\Exception\InvalidImageSizeException;
use App\Validator\ImageValidator;
use PHPUnit\Framework\TestCase;

final class ImageValidatorTest extends TestCase
{
    /**
     * @dataProvider imageValidatorProvider
     */
    public function test_image_validator_works_correct(
        string $imageString,
        string $expectedExceptionClass = null
    ): void {
        if (null !== $expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
        }

        $imageValidator = new ImageValidator();
        $imageValidator->validate($imageString);


        $this->assertTrue(true);
    }

    public function imageValidatorProvider(): \Generator
    {
        yield 'Valid png' => [
            'imageString' => \file_get_contents(__DIR__ . '/resources/valid_base64_png.txt'),
        ];

        yield 'Valid jpg' => [
            'imageString' => \file_get_contents(__DIR__ . '/resources/valid_base64_jpg.txt'),
        ];

        yield 'Invalid image' => [
            'imageString' => \file_get_contents(__DIR__ . '/resources/invalid_img.txt'),
            'expectedExceptionClass' => InvalidImageException::class
        ];

        yield 'Invalid image format' => [
            'imageString' => \file_get_contents(__DIR__ . '/resources/invalid_img_format.txt'),
            'expectedExceptionClass' => InvalidImageFormatException::class
        ];
    }
}