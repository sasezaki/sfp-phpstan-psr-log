<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Type;

use PHPStan\Testing\TypeInferenceTestCase;

class ContextTypeSpecifyingExtensionTest extends TypeInferenceTestCase
{
    /** @phpstan-return iterable<mixed> */
    public function dataFileAsserts(): iterable
    {
        yield from $this->gatherAssertTypes(__DIR__ . '/data/contextTypeSpecifyingExtension/context.php');
    }

    /**
     * @dataProvider dataFileAsserts
     * @param mixed ...$args
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
        ...$args
    ): void {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    /** @return string[] */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/data/contextTypeSpecifyingExtension/config.neon'];
    }
}
