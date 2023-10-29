<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\Psr\Log\Rules\ContextRequireExceptionKeyRule;

use function count;

/**
 * @extends RuleTestCase<ContextRequireExceptionKeyRule>
 * @covers \Sfp\PHPStan\Psr\Log\Rules\ContextRequireExceptionKeyRule
 */
final class ContextRequireExceptionKeyRuleTest extends RuleTestCase
{
    /** @var bool */
    private $treatPhpDocTypesAsCertain = false;

    /** @var bool */
    private $reportMaybes = false;

    /** @phpstan-var 'debug'|'info' */
    private $reportContextExceptionLogLevel = 'debug';

    protected function getRule(): Rule
    {
        return new ContextRequireExceptionKeyRule(
            $this->treatPhpDocTypesAsCertain,
            $this->reportMaybes,
            $this->reportContextExceptionLogLevel
        );
    }

    /** @test */
    public function shouldNotBeReportedIfLogLevelIsNotReached(): void
    {
        $this->reportContextExceptionLogLevel = 'info';

        $this->analyse([__DIR__ . '/data/ContextRequireExceptionKeyRule/reportContextExceptionLogLevel.php'], [
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::info() requires \'exception\' key. Current scope has Throwable variable - $exception',
                13,
            ],
        ]);
    }

    /**
     * @testWith [ false, false, 0, [] ]
     *           [ false, true, 0, [] ]
     *           [ true, false, 1, [27] ]
     *           [ true, true,  2, [24, 27] ]
     */
    public function testParameterSettings(
        bool $treatPhpDocTypesAsCertain,
        bool $reportMaybes,
        int $expectedErrorCount,
        array $expectedErrorLines
    ): void {
        $this->treatPhpDocTypesAsCertain      = $treatPhpDocTypesAsCertain;
        $this->reportMaybes                   = $reportMaybes;
        $this->reportContextExceptionLogLevel = 'debug';

        $errors = $this->gatherAnalyserErrors([__DIR__ . '/data/ContextRequireExceptionKeyRule/parameters.php']);

        $this->assertSame($expectedErrorCount, count($errors));

        $errorLines = [];
        foreach ($errors as $error) {
            $errorLines[] = $error->getLine();
        }

        $this->assertSame($expectedErrorLines, $errorLines);
    }

    /**
     * @test
     */
    public function testProcessNode(): void
    {
        $this->treatPhpDocTypesAsCertain      = true;
        $this->reportMaybes                   = false;
        $this->reportContextExceptionLogLevel = 'debug';
        $this->analyse([__DIR__ . '/data/contextRequireExceptionKey.php'], [
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                24,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                25,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::log() requires \'exception\' key. Current scope has Throwable variable - $exception',
                28,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::log() requires \'exception\' key. Current scope has Throwable variable - $exception',
                29,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                33,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                35,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                37,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception',
                38,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::notice() requires \'exception\' key. Current scope has Throwable variable - $exception2',
                53,
            ],
            [
                'Parameter $context of logger method Psr\Log\LoggerInterface::log() requires \'exception\' key. Current scope has Throwable variable - $exception2',
                54,
            ],
        ]);
    }
}
