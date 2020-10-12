<?php
declare(strict_types=1);

namespace TestSeparator\Service;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        printf('%s: %s' . PHP_EOL, $level, $message);
    }
}