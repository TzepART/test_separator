<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Psr\Log\LoggerInterface;
use TestSeparator\Configuration;

class LazyTestSeparatorHandler implements TestsSeparatorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    public function separateTests(int $countSuit): void
    {
        // TODO: Implement separateTests() method.
    }
}