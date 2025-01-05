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
use Sfp\PHPStan\Psr\Log\Analyser\Psr3ContextTypeProvider;

use function in_array;

final class ContextTypeSpecifyingExtension implements MethodTypeSpecifyingExtension
{
    /** @var bool */
    private $enabled;

    /** @var ContextTypeProviderInterface */
    private $contextTypeProvider;

    public function __construct(
        bool $enabled = true,
        ?ContextTypeProviderInterface $contextTypeProvider = null
    ) {
        $this->enabled             = $enabled;
        $this->contextTypeProvider = $contextTypeProvider ?? new Psr3ContextTypeProvider();
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     * @return 'Psr\Log\LoggerInterface'
     */
    public function getClass(): string
    {
        /** @psalm-suppress UndefinedClass */
        return 'Psr\Log\LoggerInterface';
    }

    public function isMethodSupported(
        MethodReflection $methodReflection,
        MethodCall $node,
        TypeSpecifierContext $context
    ): bool {
        if (! $this->enabled) {
            return false;
        }

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
