<?php

declare(strict_types=1);

/**
 * @phpstan-param array{exception: \Exception} $context
 * @phpstan-param array{exception: \Exception}|array{exception2: \Exception} $unionContext
 */
function main(
    Psr\Log\LoggerInterface $logger,
    array $nonTypedArray,
    array $context,
    array $unionContext
): void {
    try {
        throw new InvalidArgumentException();
    } catch (InvalidArgumentException $exception) {
        $logger->notice('foo', $nonTypedArray);
        $logger->notice('foo', $context);
        $logger->notice('foo', $unionContext);

        $logger->notice('foo', array_merge(['foo' => 1],  ['exception' => $exception] ));
        $logger->notice('foo', array_merge(['foo' => 1],  ['exception2' => $exception] ));
    }
}
