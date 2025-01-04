<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser\BigQuery;

use Exception;
use PHPStan\Type\Type;
use Sfp\PHPStan\Psr\Log\Analyser\ContextTypeProviderInterface;

use function file_get_contents;
use function json_decode;

final class ContextTypeFromTableFieldSchemaJsonFileProvider implements ContextTypeProviderInterface
{
    /** @var string */
    private $schemaFile;

    /** @var TableFieldSchemaJsonPayloadTypeConverterInterface */
    private $tableFieldSchemaJsonPayloadTypeConverter;

    /** @var ?array */
    private $jsonPayloadFields;

    public function __construct(
        string $schemaFile,
        TableFieldSchemaJsonPayloadTypeConverterInterface $tableFieldSchemaJsonPayloadTypeConverter
    ) {
        $this->schemaFile                               = $schemaFile;
        $this->tableFieldSchemaJsonPayloadTypeConverter = $tableFieldSchemaJsonPayloadTypeConverter;
    }

    public function getType(): Type
    {
        return $this->tableFieldSchemaJsonPayloadTypeConverter->toArrayType($this->getJsonPayloadFields());
    }

    private function getJsonPayloadFields(): array
    {
        if (! isset($this->jsonPayloadFields)) {
            $schemaJson = file_get_contents($this->schemaFile);
            $schema     = json_decode($schemaJson, true);

            $jsonPayloadFields = null;
            foreach ($schema as $item) {
                if ($item['name'] !== 'jsonPayload') {
                    continue;
                }
                $jsonPayloadFields = $item['fields'];
            }

            if (! $jsonPayloadFields) {
                throw new Exception('schemaFile must have jsonPayload field');
            }

            $this->jsonPayloadFields = $jsonPayloadFields;
        }

        return $this->jsonPayloadFields;
    }
}
