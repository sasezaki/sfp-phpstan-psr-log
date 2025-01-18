<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\ContextType;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-param array{exception?: Exception} $context2
 */
function main(
    LoggerInterface $logger,
    array $context2
): void {
//    $logger->info('info', ['exception' => $throwable->getMessage()]);
    $logger->info('info', $context2);
}
