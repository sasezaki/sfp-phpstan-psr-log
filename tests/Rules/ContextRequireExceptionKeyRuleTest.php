<?php

namespace SfpTest\PHPStan\Psr\Log\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\Psr\Log\Rules\ContextRequireExceptionKeyRule;

/**
 * @implements RuleTestCase<ContextRequireExceptionKeyRule>
 */
class ContextRequireExceptionKeyRuleTest extends RuleTestCase
{
    protected function getRule() : Rule
    {
        return new ContextRequireExceptionKeyRule();
    }

    /** @test */
    public function testProcessNode()
    {
        $this->analyse([__DIR__ . '/data/contextRequireExceptionKey.php'], [
            'missing context' => [
                'Parameter $context of logger method info requires \'exception\' key. Current scope has Throwable variable - $exception',
                13,
            ],
            'invalid key' => [
                'Parameter $context of logger method info requires \'exception\' key. Current scope has Throwable variable - $exception',
                14,
            ],
            'missing context - log method' => [
                'Parameter $context of logger method log requires \'exception\' key. Current scope has Throwable variable - $exception',
                17,
            ],
            'invalid key - log method' => [
                'Parameter $context of logger method log requires \'exception\' key. Current scope has Throwable variable - $exception',
                18,
            ],
            'missing context - other catch' => [
                'Parameter $context of logger method critical requires \'exception\' key. Current scope has Throwable variable - $exception2',
                24,
            ],
            'invalid key - other catch' => [
                'Parameter $context of logger method log requires \'exception\' key. Current scope has Throwable variable - $exception2',
                25,
            ],
        ]);
    }
}
