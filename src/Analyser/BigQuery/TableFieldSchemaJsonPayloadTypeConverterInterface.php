<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use PHPStan\Type\Constant\ConstantArrayType;

/**
 * @see https://cloud.google.com/bigquery/docs/reference/rest/v2/tables?hl=en#TableFieldSchema
 *
 * @phpstan-type non_record_field_type 'STRING'|'BYTES'|'INTEGER'|'INT64'|'FLOAT'|'FLOAT64'|'BOOLEAN'|'BOOL'|'TIMESTAMP'|'DATE'|'TIME'|'DATETIME'|'GEOGRAPHY'|'NUMERIC'|'BIGNUMERIC'|'JSON'|'RANGE'
 * @phpstan-type field_type non_record_field_type|'RECORD'|'STRUCT'
 * @phpstan-type schema_item array{name: string, type: field_type, mode?: 'NULLABLE'|'REQUIRED'|'REPEATED', fields?: list<array{name: string, type: string}>}
 */
interface TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields): ConstantArrayType;
}
