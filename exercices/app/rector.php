<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\Php84\Rector\Class_\PropertyHookRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\TypeDeclaration\Rector\StrictStringTypes;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        __DIR__ . '/migrations',
    ])
    ->withPhpVersion(\Rector\ValueObject\PhpVersion::PHP_84)
    ->withRules([
        ExplicitNullableParamTypeRector::class,
        ReadOnlyPropertyRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]);