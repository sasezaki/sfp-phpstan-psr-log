<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

use function in_array;

/**
 * @phpstan-type schema_item from TableFieldSchemaJsonPayloadTypeConverterInterface
 */
final class GenericTableFieldSchemaJsonPayloadTypeConverter implements TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields): Type
    {
        return self::convertFieldsToTypes($jsonPayloadFields);
    }

    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public static function convertFieldsToTypes(array $jsonPayloadFields): Type
    {
        $keyTypes        = [];
        $valueTypes      = [];
        $nextAutoIndexes = [0]; // ((index is intended non-numeric-string, so int never used.)) // todo unexpected numeric-string is used
        $optionalKeys    = [];

        $idx = 0;
        foreach ($jsonPayloadFields as $item) {
            $keyTypes[] = new ConstantStringType($item['name']);

            if ($item['type'] === 'RECORD' || $item['type'] === 'STRUCT') {
                if ($item['mode'] === 'REPEATED') {
                    // todo...
                }

                // @todo reverse (todo) json_encode array
                // eg.
                // json_encode(["pub_date" => new \DateTime])
                // would be like {"pub_date":{"date":"2025-01-04 10:00:00.396494","timezone_type":3,"timezone":"UTC"}}.
                // , if RECORD has 'date', 'timezone_type' & 'timezone' field, it would be `{pub_date: \DateTimeInterface}`

                $valueTypes[] = self::convertFieldsToTypes($item['fields']);
            } else {
                $valueTypes[] = self::convertTypeToPhpScalarType($item['type']);
            }
            $optionalKeys[] = $idx;
            ++$idx;
        }

        return new ConstantArrayType($keyTypes, $valueTypes, $nextAutoIndexes, $optionalKeys);
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
