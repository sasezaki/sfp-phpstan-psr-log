<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use PHPStan\Type\Constant\ConstantArrayType;

/**
 * @phpstan-type schema_item array{name: string, type: string, mode?: 'NULLABLE'|'REQUIRED'|'REPEATED', fields?: list<array{name: string, type: string}>}
 */
interface TableFieldSchemaJsonPayloadTypeConverterInterface
{
    /**
     * @param list<schema_item> $jsonPayloadFields
     */
    public function toArrayType(array $jsonPayloadFields): ConstantArrayType;
}
