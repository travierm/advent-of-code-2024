<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

function getLogger()
{
    $handler = new StreamHandler('php://stdout');
    $handler->setFormatter(new ColoredLineFormatter());

    $logger = new Logger('main');
    $logger->pushHandler($handler);

    return $logger;
}

function arrayWithoutIndex(array $array, int $index)
{
    return array_merge(
        array_slice($array, 0, $index),
        array_slice($array, $index + 1)
    );
}
