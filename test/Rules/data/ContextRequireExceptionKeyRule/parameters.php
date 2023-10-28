<?php

declare(strict_types=1);

/**
 * @phpstan-param array{exception: \Exception}|array{exception2: \Exception} $unionContext
 */
function main(
    Psr\Log\LoggerInterface $logger,
    array $unionContext
): void {
    try {
        throw new InvalidArgumentException();
    } catch (InvalidArgumentException $exception) {
        $logger->notice('foo', array_merge(['foo' => 1],  ['exception' => $exception] ));

        $logger->notice('foo', $unionContext);
        // todo handle with array_merge result
        $logger->notice('foo', array_merge(['foo' => 1],  ['exception2' => $exception] ));
    }
}
