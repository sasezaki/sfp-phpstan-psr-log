<?php

declare(strict_types=1);

function main(
    Psr\Log\LoggerInterface $logger,
    Throwable $throwable
): void {
    $logger->info('info', ['exception' => $throwable->getMessage()]);
}
