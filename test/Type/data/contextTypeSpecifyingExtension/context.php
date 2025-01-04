<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Type;

use Psr\Log\LoggerInterface;

use function PHPStan\Testing\assertType;

/**
 * @var LoggerInterface $logger
 */

$logger->error('err', $arr);

assertType('array{exception?: Exception}', $arr);
