<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\Psr\Log\Rules\ContextTypeRule;

/**
 * @extends RuleTestCase<ContextTypeRule>
 * @covers \Sfp\PHPStan\Psr\Log\Rules\ContextTypeRule
 */
final class ContextTypeRuleTest extends RuleTestCase
{
    /** @phpstan-var ?ContextTypeProviderInterface */
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
                'Parameter #2 $context of method Psr\Log\LoggerInterface::info() expects array{exception?: \Throwable}, array{exception: string} given.',
                9,
            ],
        ]);
    }

    // /**
    //  * @test
    //  */
    // public function testProcessNodeWithBigQueryContextTypeProvider(): void
    // {
    //     $this->contextTypeProvider = new BigQueryContextTypeProvider;
    //     $this->analyse([__DIR__ . '/data/contextType.php'], [
    //         [
    //             // array{first_name?: string, product?: array{id?: string}, cancellation_reason?: float|int|numeric-string, cancellation_date?: \DateTimeInterface, exception?: \Throwable}',
    //             'Parameter #2 $context of method Psr\Log\LoggerInterface::info() expects array{exception?: \Throwable}, array{exception: string} given.',
    //             9,
    //         ],
    //     ]);
    // }
}
