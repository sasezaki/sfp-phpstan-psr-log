<?php

/**
 * @var \Psr\Log\LoggerInterface $logger
 */

try {
    $logger->debug("foo"); // allow
    throw new \RuntimeException();
} catch (\RuntimeException $exception) {
    $logger->info("foo");
    $logger->notice("foo", ['throwable' => $exception]);
    $logger->alert("foo", ['exception' => $exception]);
}