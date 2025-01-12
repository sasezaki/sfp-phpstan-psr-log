<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\TypeConverter\BigQuery;

use PHPUnit\Framework\TestCase;
use Sfp\PHPStan\Psr\Log\TypeConverter\BigQuery\GenericTableFieldSchemaJsonPayloadTypeConverter;

/**
 * @covers \SfpTest\PHPStan\Psr\Log\TypeConverter\BigQuery\GenericTableFieldSchemaJsonPayloadTypeConverter
 */
final class GenericTableFieldSchemaJsonPayloadTypeConverterTest extends TestCase
{
    /**
     * @see https://github.com/phpstan/phpstan-src/blob/2.1.1/tests/PHPStan/Type/Constant/ConstantArrayTypeBuilderTest.php
     */
    public function testConvertFieldsToTypes() : void
    {
        $type = GenericTableFieldSchemaJsonPayloadTypeConverter::convertFieldsToTypes([
            ['name' => '', 'type' => 'STRING'],
            ['name' => '0', 'type' => 'STRING'],
        ]);

        $this->assertSame([0, 1], $type->getNextAutoIndexes());
    }
}