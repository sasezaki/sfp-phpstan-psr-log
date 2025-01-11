<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use Exception;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use UnexpectedValueException;

use function array_column;
use function sort;

/**
 * @phpstan-import-type schema_item from TableFieldSchemaJsonPayloadTypeConverterInterface
 * @phpstan-import-type non_record_field_type from TableFieldSchemaJsonPayloadTypeConverterInterface
 */
final class GenericTableFieldSchemaJsonPayloadTypeConverter implements TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields): ConstantArrayType
    {
        return self::convertFieldsToTypes($jsonPayloadFields);
    }

    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public static function convertFieldsToTypes(array $jsonPayloadFields, int $nestedLevel = 0): ConstantArrayType
    {
        $keyTypes        = [];
        $valueTypes      = [];
        $nextAutoIndexes = [0]; // ((index is intended non-numeric-string, so int never used.)) // todo unexpected numeric-string is used
        $optionalKeys    = [];

        $idx = 0;
        foreach ($jsonPayloadFields as $item) {
            if ($item['type'] === 'RECORD' || $item['type'] === 'STRUCT') {
                if (! isset($item['mode'], $item['fields'])) {
                    throw new UnexpectedValueException('Offset mode or fields not exists');
                }

                // if ($item['mode'] === 'REPEATED') {
                    // todo...
                // }

                if ($objectType = self::reverseRecordFieldsToObjectType($item['fields'])) {
                    $valueTypes[] = $objectType;
                } else {
                    // todo consier ignore deep nested
                    // if ($nestedLevel > 2) {
                    //    continue;
                    // }
                    $valueTypes[] = self::convertFieldsToTypes($item['fields'], ++$nestedLevel);
                }
            } else {
                $valueTypes[] = self::convertTypeToPhpScalarType($item['type']);
            }

            $keyTypes[]     = new ConstantStringType($item['name']);
            $optionalKeys[] = $idx;
            ++$idx;
        }

        return new ConstantArrayType($keyTypes, $valueTypes, $nextAutoIndexes, $optionalKeys);
    }

    /**
     * eg.
     *  json_encode(["pub_date" => new \DateTime])
     * would be like {"pub_date":{"date":"2025-01-04 10:00:00.396494","timezone_type":3,"timezone":"UTC"}}.
     * if RECORD has 'date', 'timezone_type' & 'timezone' field, it would be `{pub_date: \DateTimeInterface}`
     */
    public static function reverseRecordFieldsToObjectType(array $fields): ?ObjectType
    {
        $names = array_column($fields, 'name');
        sort($names);
        if (['date', 'timezone', 'timezone_type'] === $names) {
            return new ObjectType('\DateTimeInterface');
        }

        if (['class', 'code', 'file', 'message', 'trace'] === $names || ['class', 'code', 'file', 'message', 'previous', 'trace'] === $names) {
            return new ObjectType('\Throwable');
        }

        return null;
    }

    /**
     * @todo implement
     * @phpstan-param non_record_field_type $type
     *
     * https://cloud.google.com/bigquery/docs/reference/rest/v2/tables?hl=en#TableFieldSchema
     */
    public static function convertTypeToPhpScalarType(string $type): Type
    {
        switch ($type) {
            case 'STRING':
                return new StringType();
            case 'INTEGER':
                return new IntegerType();
            case 'FLOAT':
                return TypeCombinator::union(
                    new AccessoryNumericStringType(),
                    new IntegerType(),
                    new FloatType()
                );
            case 'BOOLEAN':
                return new BooleanType();
            default:
                throw new Exception('Not supported type' . $type);
        }
    }
}
