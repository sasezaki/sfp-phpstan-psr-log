<?php

declare(strict_types=1);

namespace Sfp\PHPStan\Psr\Log\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use Sfp\PHPStan\Psr\Log\Analyser\ContextTypeProviderInterface;

use function in_array;

final class ContextTypeSpecifyingExtension implements MethodTypeSpecifyingExtension
{
    /** @var ContextTypeProviderInterface */
    private $contextTypeProvider;

    public function __construct(
        ContextTypeProviderInterface $contextTypeProvider
    ) {
        $this->contextTypeProvider = $contextTypeProvider;
    }

    public function getClass(): string
    {
        return 'Psr\Log\LoggerInterface';
    }

    public function isMethodSupported(
        MethodReflection $methodReflection,
        MethodCall $node,
        TypeSpecifierContext $context
    ): bool {
        return in_array($methodReflection->getName(), [
            'log',
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ], true);
    }

    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $contextArgIndex = 1;
        if ($methodReflection->getName() === 'log') {
            $contextArgIndex = 2;
        }

        $contextType = $this->contextTypeProvider->getType();

        return new SpecifiedTypes(['$context' => [$node->getArgs()[$contextArgIndex]->value, $contextType]]);
    }
}
