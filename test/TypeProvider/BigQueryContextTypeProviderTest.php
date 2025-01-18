<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\TypeProvider;

use PHPStan\Reflection\ReflectionProviderStaticAccessor;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use Sfp\PHPStan\Psr\Log\TypeConverter\BigQuery\GenericTableFieldSchemaJsonPayloadTypeConverter;
use Sfp\PHPStan\Psr\Log\TypeProvider\BigQueryContextTypeProvider;

class BigQueryContextTypeProviderTest extends PHPStanTestCase
{
    /**
     * @dataProvider \SfpTest\PHPStan\Psr\Log\TypeProvider\GeneralContextTypeDataProvider::provideTypes()
     */
    public function testGeneralContextType(ConstantArrayType $argType, bool $expected): void
    {
        ReflectionProviderStaticAccessor::registerInstance($this->createReflectionProvider());

        $provider = new BigQueryContextTypeProvider(
            __DIR__ . '/data/bigQuerySchema.json',
            new GenericTableFieldSchemaJsonPayloadTypeConverter()
        );
        $this->assertSame($expected, $provider->getType()->accepts($argType, true)->yes());
    }

    /**
     * @dataProvider provideTypes
     */
    public function testAgainstBigQuerySchema(ConstantArrayType $argType, bool $expected): void
    {
        ReflectionProviderStaticAccessor::registerInstance($this->createReflectionProvider());

        $provider = new BigQueryContextTypeProvider(
            __DIR__ . '/data/bigQuerySchema.json',
            new GenericTableFieldSchemaJsonPayloadTypeConverter()
        );

        $argType = new ConstantArrayType(
            [new ConstantStringType('first_name')],
            [new ObjectType(Exception::class)],
            [0],
            [0]
        );

        $this->assertFalse($provider->getType()->accepts($argType, true)->yes());
    }

    public static function provideTypes(): array
    {
        return [
            "array{first_name?: string}"    => [
                new ConstantArrayType(
                    [new ConstantStringType('first_name')],
                    [new StringType()],
                    [0],
                    [0]
                ),
                true,
            ],
            "array{first_name?: Exception}" => [
                new ConstantArrayType(
                    [new ConstantStringType('first_name')],
                    [new ObjectType(Exception::class)],
                    [0],
                    [0]
                ),
                false,
            ],
        ];
    }
}
