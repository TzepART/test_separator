<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Psr\Log\LoggerInterface;
use TestSeparator\Configuration;

class TestsSeparatorFactory
{
    public static function makeTestsSeparator(Configuration $configuration, LoggerInterface $logger): TestsSeparatorInterface
    {
        $separateHandler = new TestsSeparatorHandler(
            ServicesSeparateTestsFactory::makeTestFilePathHelper($configuration),
            ServicesSeparateTestsFactory::makeLevelDeepService($configuration->getDepthLevel()),
            $configuration->getResultPath(),
            $logger
        );

        return $separateHandler;
    }
}