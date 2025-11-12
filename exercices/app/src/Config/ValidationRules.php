<?php

declare(strict_types=1);

namespace App\Config;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ValidationRules
{
    /**
     * @return list<Constraint>
     */
    public static function getTitleConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'Title is required'),
            new Assert\Length(
                min: 3,
                max: 255,
                minMessage: 'Title must be at least {{ limit }} characters',
                maxMessage: 'Title must not exceed {{ limit }} characters'
            ),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getAuthorConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'Author is required'),
            new Assert\Length(
                min: 3,
                max: 255,
                minMessage: 'Author must be at least {{ limit }} characters',
                maxMessage: 'Author must not exceed {{ limit }} characters'
            ),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getYearConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'Year is required'),
            new Assert\GreaterThan(
                value: 1000,
                message: 'Year must be greater than {{ compared_value }}'
            ),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getIsbnConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'ISBN is required'),
            new Assert\Isbn(message: 'ISBN must be valid'),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getPriceConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'Price is required'),
            new Assert\GreaterThan(
                value: 0,
                message: 'Price must be greater than {{ compared_value }}'
            ),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getCategoryIdConstraints(): array
    {
        return [
            new Assert\NotBlank(message: 'Category ID is required'),
            new Assert\GreaterThan(
                value: 0,
                message: 'Category ID must be greater than {{ compared_value }}'
            ),
        ];
    }

    /**
     * @return list<Constraint>
     */
    public static function getDescriptionConstraints(): array
    {
        return [
            new Assert\Length(
                max: 5000,
                maxMessage: 'Description must not exceed {{ limit }} characters'
            ),
        ];
    }
}
