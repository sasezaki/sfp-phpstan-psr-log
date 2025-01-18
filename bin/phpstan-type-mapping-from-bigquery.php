#!/usr/bin/env php
<?php

declare(strict_types=1);

use PHPStan\PhpDocParser\Printer\Printer;
use Sfp\PHPStan\Psr\Log\TypeConverter\BigQuery\GenericTableFieldSchemaJsonPayloadTypeConverter;
use Sfp\PHPStan\Psr\Log\TypeProvider\BigQueryContextTypeProvider;

/**
 * borrowed from doctrine/sql-formatter 's sql-formatter
 */

if ("cli" !== php_sapi_name()) {
    echo "<p>Run this PHP script from the command line to see mapping BigQuery schema to PHPStan Type.  It supports Unix pipes or command line argument style.</p>";
    exit;
}

if (isset($argv[1])) {
    $schema = $argv[1];
} else {
    $schema = stream_get_contents(fopen('php://stdin', 'r'));
}

$autoloadFiles = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        break;
    }
}

if (! class_exists(Printer::class)) {
    echo 'require phpstan/phpdoc-parser Printer class' . PHP_EOL;
    exit(1);
}

$converter = new GenericTableFieldSchemaJsonPayloadTypeConverter();
$provider  = new BigQueryContextTypeProvider(
    'data://,' . $schema,
    $converter
);

echo (new Printer())->print($provider->getType()->toPhpDocNode());
