<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\Psr\Log\Rules\ContextTypeRule;
use Sfp\PHPStan\Psr\Log\TypeConverter\BigQuery\GenericTableFieldSchemaJsonPayloadTypeConverter;
use Sfp\PHPStan\Psr\Log\TypeProvider\BigQueryContextTypeProvider;

use function sprintf;

/**
 * @extends RuleTestCase<ContextTypeRule>
 * @covers \Sfp\PHPStan\Psr\Log\Rules\ContextTypeRule
 */
final class ContextTypeRuleTest extends RuleTestCase
{
    /** @phpstan-var null|\Sfp\PHPStan\Psr\Log\TypeProvider\ContextTypeProviderInterface */
    private $contextTypeProvider;

    protected function getRule(): Rule
    {
        return new ContextTypeRule($this->contextTypeProvider);
    }

    /**
     * @test
     */
    public function testProcessNode(): void
    {
        $this->contextTypeProvider = null;
        $this->analyse([__DIR__ . '/data/contextType.php'], [
            [
                sprintf(
                    'Parameter #2 $context of method Psr\Log\LoggerInterface::info() expects %s, array{exception: string} given.',
                    'array{exception?: Throwable}'
                ),
                14,
            ],
        ]);
    }

     /**
      * @test
      */
    public function testProcessNodeWithBigQueryContextTypeProvider(): void
    {
        $this->contextTypeProvider = new BigQueryContextTypeProvider(__DIR__ . '/../TypeProvider/data/bigQuerySchema.json', new GenericTableFieldSchemaJsonPayloadTypeConverter());
        $this->analyse([__DIR__ . '/data/contextType.php'], [
            [
                sprintf(
                    'Parameter #2 $context of method Psr\Log\LoggerInterface::info() expects %s, array{exception: string} given.',
                    'array{first_name?: string, product?: array{id?: string}, cancellation_reason?: (float | int | numeric-string), cancellation_date?: \DateTimeInterface, exception?: \Throwable}'
                ),
                14,
            ],
        ]);
    }
}
