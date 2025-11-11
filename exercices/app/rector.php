<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withComposerBased(phpunit: true, symfony: true, doctrine: true)
    ->withPhpSets(php84: true)
    ->withTypeCoverageLevel(8)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        earlyReturn: true,
    )
    ->withSkip([
        // Add specific rules to skip if needed
    ]);