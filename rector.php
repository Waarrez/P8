<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()

    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])

    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    );
