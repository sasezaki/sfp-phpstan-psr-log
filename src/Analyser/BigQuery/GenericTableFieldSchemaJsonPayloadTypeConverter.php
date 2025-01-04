<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Thowable;

use function in_array;

/**
 * > The name and type fields are required. All other fields are optional.
 * https://cloud.google.com/bigquery/docs/schemas?hl=en#creating_a_JSON_schema_file
 *
 * - type
 *   https://cloud.google.com/bigquery/docs/reference/rest/v2/tables?hl=en#TableFieldSchema
 *   >  The field data type. Possible values include:
 *   STRING, BYTES, INTEGER (or INT64), FLOAT (or FLOAT64), ...., NUMERIC, BIGNUMERIC, JSON, RECORD (or STRUCT), RANGE
 *
 * - mode
 *   > Optional. The field mode. Possible values include NULLABLE, REQUIRED and REPEATED.
 *     The default value is NULLABLE.
 *
 * @phpstan-type schema_item from TableFieldSchemaJsonPayloadTypeConverterInterface
 */
final class GenericTableFieldSchemaJsonPayloadTypeConverter implements TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields): Type
    {
        $builder = ConstantArrayTypeBuilder::createFromConstantArray(
            self::convertFieldsToTypes($jsonPayloadFields)
        );

        $builder->setOffsetValueType(
            new ConstantStringType('exception'),
            new ObjectType(Thowable::class),
            true
        );

        return $builder->getArray();
    }

    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public static function convertFieldsToTypes(array $jsonPayloadFields): Type
    {
        // private array $keyTypes,
        // private array $valueTypes,
        // int|array $nextAutoIndexes = [0],
        // private array $optionalKeys = [],

        $keyTypes        = [];
        $valueTypes      = [];
        $nextAutoIndexes = [0];
        $optionalKeys    = [];

        $idx = 0;
        foreach ($jsonPayloadFields as $item) {
            $keyTypes[] = new ConstantStringType($item['name']);

            if ($item['type'] === 'RECORD' || $item['type'] === 'STRUCT') {
                if ($item['mode'] === 'REPEATED') {
                    // todo...
                }
                $valueTypes[] = self::convertFieldsToTypes($item['fields']);
            } else {
                $valueTypes[] = self::convertTypeToPhpScalarType($item['type']);
            }
            $optionalKeys[] = $idx;
            ++$idx;
        }

        return new ConstantArrayType($keyTypes, $valueTypes, $nextAutoIndexes, $optionalKeys);
    }

    public function convertItem(array $item)
    {
    }

    /**
     * @todo implement
     *
     * https://cloud.google.com/bigquery/docs/reference/rest/v2/tables?hl=en#TableFieldSchema
     */
    public static function convertTypeToPhpScalarType(string $type, bool $floatNonStrict = true): Type
    {
        if (in_array($type, ['INTEGER', 'INT64'])) {
            return new IntegerType();
        }

        if ($type === 'FLOAT' && $floatNonStrict) {
            return TypeCombinator::union(
                new AccessoryNumericStringType(),
                new IntegerType(),
                new FloatType()
            );
        }

        if ($type === 'FLOAT') {
            return new FloatType();
        }

        // todo implment other types...

        return new StringType();
    }
}
