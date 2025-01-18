<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\TypeProvider;

use Exception;
use PHPStan\Reflection\ReflectionProviderStaticAccessor;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use Sfp\PHPStan\Psr\Log\TypeProvider\Psr3ContextTypeProvider;
use Throwable;

class Psr3ContextTypeProviderTest extends PHPStanTestCase
{
    /**
     * eg.
     * ```
     * [@]param array{} $context
     * public function testLog(array $context) {
     *     $this->logger->info('foo, $context);
     * }
     * ```
     *
     * @dataProvider provideTypes
     */
    public function testTypeProvider(ConstantArrayType $argType, bool $expected): void
    {
        ReflectionProviderStaticAccessor::registerInstance($this->createReflectionProvider());

        $provider = new Psr3ContextTypeProvider();

        $this->assertSame($expected, $provider->getType()->accepts($argType, true)->yes());
    }

    public function provideTypes(): array
    {
        return [
            'array{}'                        => [
                new ConstantArrayType([], []),
                true,
            ],
            "array{non-exception: 'string'}" => [
                new ConstantArrayType(
                    [new ConstantStringType('non-exception')],
                    [new ConstantStringType('string')]
                ),
                true,
            ],
            "array{exception?: 'string'}"    => [
                new ConstantArrayType(
                    [new ConstantStringType('exception')],
                    [new ConstantStringType('string')],
                    [0],
                    [0]
                ),
                false,
            ],
            "array{exception?: \Throwable}"  => [
                new ConstantArrayType(
                    [new ConstantStringType('exception')],
                    [new ObjectType(Throwable::class)],
                    [0],
                    [0]
                ),
                true,
            ],
            "array{exception?: \Exception}"  => [
                new ConstantArrayType(
                    [new ConstantStringType('exception')],
                    [new ObjectType(Exception::class)],
                    [0],
                    [0]
                ),
                true,
            ],
        ];
    }
}
