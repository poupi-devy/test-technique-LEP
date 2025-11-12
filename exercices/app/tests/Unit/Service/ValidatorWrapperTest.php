<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\BookImportData;
use App\DTO\ValidationResult;
use App\Service\ValidationErrorFormatter;
use App\Service\ValidatorWrapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorWrapperTest extends KernelTestCase
{
    private ValidatorWrapper $validatorWrapper;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $formatter = new ValidationErrorFormatter();
        $this->validatorWrapper = new ValidatorWrapper($this->validator, $formatter);
    }

    public function testValidateWithValidData(): void
    {
        $bookData = new BookImportData(
            title: 'Valid Title',
            author: 'Valid Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $result = $this->validatorWrapper->validate($bookData);

        self::assertInstanceOf(ValidationResult::class, $result);
        self::assertTrue($result->isValid);
        self::assertEmpty($result->errors);
    }

    public function testValidateWithInvalidData(): void
    {
        $bookData = new BookImportData(
            title: 'X', // Too short
            author: '', // Missing
            year: 500, // Too low
            isbn: 'invalid_isbn',
        );

        $result = $this->validatorWrapper->validate($bookData);

        self::assertFalse($result->isValid);
        self::assertNotEmpty($result->errors);
        // Errors could include Length for title + NotBlank for author + GreaterThan for year + Isbn for isbn
        self::assertGreaterThanOrEqual(4, count($result->errors));
    }

    public function testValidateReturnsFormattedErrors(): void
    {
        $bookData = new BookImportData(
            title: '', // Required
            author: 'Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $result = $this->validatorWrapper->validate($bookData);

        self::assertFalse($result->isValid);
        self::assertGreaterThanOrEqual(1, count($result->errors));

        $error = $result->errors[0];
        self::assertArrayHasKey('field', $error);
        self::assertArrayHasKey('message', $error);
    }

    public function testIsValidWithValidData(): void
    {
        $bookData = new BookImportData(
            title: 'Valid Title',
            author: 'Valid Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $isValid = $this->validatorWrapper->isValid($bookData);

        self::assertTrue($isValid);
    }

    public function testIsValidWithInvalidData(): void
    {
        $bookData = new BookImportData(
            title: '', // Required
            author: 'Author',
            year: 2024,
            isbn: '9780201633610',
        );

        $isValid = $this->validatorWrapper->isValid($bookData);

        self::assertFalse($isValid);
    }

    public function testValidateYearConstraint(): void
    {
        $bookData = new BookImportData(
            title: 'Title',
            author: 'Author',
            year: 999, // Must be >= 1000
            isbn: '9780201633610',
        );

        $result = $this->validatorWrapper->validate($bookData);

        self::assertFalse($result->isValid);
        self::assertCount(1, $result->errors);
    }

    public function testValidateIsbnConstraint(): void
    {
        $bookData = new BookImportData(
            title: 'Title',
            author: 'Author',
            year: 2024,
            isbn: 'not_an_isbn',
        );

        $result = $this->validatorWrapper->validate($bookData);

        self::assertFalse($result->isValid);
    }
}
