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
                'Parameter $context of logger method info expects \'exception\' key. Current scope has Throwable variable - $exception',
                11,
            ],
            'invalid key' => [
                'Parameter $context of logger method notice expects \'exception\' key. Current scope has Throwable variable - $exception',
                12,
            ],
        ]);
    }
}
