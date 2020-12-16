<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Psr\Log\LoggerInterface;
use TestSeparator\Configuration;
use TestSeparator\Service\FileSystemHelper;

class TestsSeparatorFactory
{
    public static function makeTestsSeparator(Configuration $configuration, LoggerInterface $logger): TestsSeparatorInterface
    {
        if ($configuration->getSeparatingStrategy() === ServicesSeparateTestsFactory::DEFAULT_GROUP_STRATEGY) {
            return new LazyTestSeparatorHandler(
                $configuration,
                $logger
            );
        }

        return new TestsSeparatorHandler(
            ServicesSeparateTestsFactory::makeTestFilePathHelper($configuration),
            ServicesSeparateTestsFactory::makeLevelDeepService($configuration->getDepthLevel()),
            $configuration->getResultPath(),
            $logger
        );
    }
}