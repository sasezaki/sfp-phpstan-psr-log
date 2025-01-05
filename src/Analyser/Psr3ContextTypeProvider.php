<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Analyser;

use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class Psr3ContextTypeProvider implements ContextTypeProviderInterface
{
    public function getType(): Type
    {
        return new ConstantArrayType(
            [new ConstantStringType('exception')],
            [new ObjectType('\Throwable')],
            [0],
            [true]
        );
    }
}
