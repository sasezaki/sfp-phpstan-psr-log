<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Type;

use Psr\Log\LoggerInterface;

use function PHPStan\Testing\assertType;

/**
 * @var LoggerInterface $logger
 */

$logger->error('err', $context); /** @phpstan-ignore variable.undefined */

assertType(
    'array{first_name?: string, product?: array{id?: string}, cancellation_reason?: float|int|numeric-string, exception?: \Throwable}', 
    $context /** @phpstan-ignore variable.undefined */
);
