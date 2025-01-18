<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\TypeProvider;

use PHPStan\Type\Constant\ConstantArrayType;

interface ContextTypeProviderInterface
{
    public function getType(): ConstantArrayType;
}
