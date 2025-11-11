<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->paths(['src', 'tests']);

    $config->skip([
        VisibilityRequiredFixer::class => ['*/migrations/*'],
    ]);

    $config->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, []);
    $config->ruleWithConfiguration(PhpdocSeparationFixer::class, ['groups' => [['ORM\\*']]]);

    // PSR-12 standard
    $config->sets([\Symplify\EasyCodingStandard\ValueObject\Set\SetList::PSR_12]);
};