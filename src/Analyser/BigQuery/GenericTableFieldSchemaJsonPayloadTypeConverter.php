<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

// use PHPStan\PhpDocParser\Ast\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;

use PHPStan\Type\IntersectionType;

use PHPStan\Type\TypeCombinator;

/**
 *
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
 * @phpstan-type schema_item = array{name: string, type: string, mode?: 'NULLABLE'|'REQUIRED'|'REPEATED'}
 */
final class GenericTableFieldSchemaJsonPayloadTypeConverter implements TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields) : Type
    {

        $builder = ConstantArrayTypeBuilder::createFromConstantArray(
            // self::itemToNode($jsonPayloadFields)
            // array_map
            self::convertType($jsonPayloadFields)
        );

        $builder->setOffsetValueType(
            new ConstantStringType('exception'),
            new ObjectType(\Thowable::class),
            true
        );

        return $builder->getArray();
    }

    public static function convertType(array $jsonPayloadFields) : Type
    {
        // private array $keyTypes,
		// private array $valueTypes,
		// int|array $nextAutoIndexes = [0],
		// private array $optionalKeys = [],

        $keyTypes = [];
        $valueTypes = [];
        $nextAutoIndexes = [0];
        $optionalKeys = [];

        $idx = 0;
        foreach($jsonPayloadFields as $item) {
            $keyTypes[] = new ConstantStringType($item['name']);
            // dummy
            // $valueTypes[] = new ConstantStringType($item['name']);
            $valueTypes[] = self::convertTypeToPhpScalarType($item['type']);
            $optionalKeys[] = $idx;
            ++$idx;
        }


        // return new ConstantArrayType(
        //     [
        //         new ConstantStringType('exception')
        //     ], 
        //     [
        //         new ObjectType(\Exception::class),
        //     ],
        //     [
        //         // nextAutoIndexes
        //         0
        //     ],
        //     [
        //         // optionalKeys 
        //         0
        //     ],
        // );

        return new ConstantArrayType($keyTypes, $valueTypes, $nextAutoIndexes, $optionalKeys);
    }

    /**
     * @param schema_item $item
     */
    public static function convertTypeToPhpScalarType(string $type, bool $floatNonStrict = true) : Type
    {
        if (in_array($type, ['INTEGER', 'INT64'])) {
            return new IntegerType;
        }

        if ($type === 'FLOAT' && $floatNonStrict) {
            // return 'numeric-string|int|float';
            return TypeCombinator::union(
                new IntegerType,
                new StringType
            );
        }

        return new StringType;

        // if (in_array($type, ['FLOAT ', 'FLOAT64'])) {
        //     return 'float';
        // }


        // return new ConstantStringType();

        // return new ConstantArrayType(
        //     [
        //         new ConstantStringType('exception')
        //     ], 
        //     [
        //         new ObjectType(\Exception::class),
        //     ],
        //     [
        //         // nextAutoIndexes
        //         0
        //     ],
        //     [
        //         // optionalKeys 
        //         0
        //     ],
        // );

        // return new ArrayType();

        // if ($item['type'] === 'RECORD' || $item['type'] === 'STRUCT') {
        //     if ($item['mode'] === 'REPEATED') {
        //         // todo...
        //     }

        //     return new Type\ArrayShapeItemNode(
        //         new Type\IdentifierTypeNode($item['name']),
        //         $item['mode'] === 'NULLABLE',
        //         new Type\ArrayShapeNode(
        //             array_map('self::itemToNode', $item['fields'])
        //         )
        //     );
        // }

        // $optional = true;
        // if (isset($item['mode']) && $item['mode'] !== 'NULLABLE') {
        //     $optional = false;
        // }

        // return new Type\ArrayShapeItemNode(
        //     new Type\IdentifierTypeNode($item['name']),
        //     $optional,
        //     new Type\IdentifierTypeNode(self::convertTypeToPhpScalarType($item['type']))
        // );
    }

    // /**
    //  * @todo implement
    //  *
    //  * https://cloud.google.com/bigquery/docs/reference/rest/v2/tables?hl=en#TableFieldSchema
    //  */
    // public static function convertTypeToPhpScalarType(string $type, bool $floatNonStrict = true)
    // {
    //     if (in_array($type, ['INTEGER', 'INT64'])) {
    //         return 'int';
    //     }

    //     if ($type === 'FLOAT' && $floatNonStrict) {
    //         return 'numeric-string|int|float';
    //     }

    //     if (in_array($type, ['FLOAT ', 'FLOAT64'])) {
    //         return 'float';
    //     }

    //     // fallback to string
    //     return 'string';
    // }
}
