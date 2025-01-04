<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\Psr\Log\Type;

/**
 * @var \Psr\Log\LoggerInterface $logger;
 */

$logger->error('err', $arr);

\PHPStan\Testing\assertType('array{exception?: Exception}', $arr);
\PHPStan\Testing\assertType('array{exception2?: Exception}', $arr);