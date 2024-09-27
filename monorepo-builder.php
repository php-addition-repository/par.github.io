<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;

return static function(MBConfig $mbConfig): void {
    $mbConfig->defaultBranch('main');
    $mbConfig->packageDirectories([__DIR__ . '/packages']);
    $mbConfig->packageAliasFormat('<major>.<minor>.x-dev');

    $mbConfig->dataToAppend(
        [
            ComposerJsonSection::REQUIRE_DEV => [
                'phpunit/phpunit' => '^11.3',
                'symplify/monorepo-builder' => '^11.2',
            ],
        ]
    );
};
