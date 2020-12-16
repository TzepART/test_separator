<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Psr\Log\LoggerInterface;
use TestSeparator\Configuration;
use TestSeparator\Exception\NeededCountGroupsAndCountDefaultGroupsNotEqual;
use TestSeparator\Exception\NeededCountGroupsAndCountResultGroupsNotEqual;
use TestSeparator\Service\FileSystemHelper;

class LazyTestSeparatorHandler implements TestsSeparatorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Configuration $configuration, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    public function separateTests(int $countSuit): void
    {
        // validate that count files in $this->configuration->getDefaultGroupsDir() === $countSuit
        $countDefaultGroups = FileSystemHelper::getCountNotEmptyFilesInDir($this->configuration->getDefaultGroupsDir());
        if ($countDefaultGroups !== $countSuit) {
            throw new NeededCountGroupsAndCountDefaultGroupsNotEqual(
                sprintf('Needed count groups and count default groups not equal. Expected - %d, got - %d.',
                    $countSuit,
                    $countDefaultGroups
                )
            );
        }

        // copy all files from $this->configuration->getDefaultGroupsDir() to $this->configuration->getResultPath()
        FileSystemHelper::copyAllFilesFromDirToDir($this->configuration->getDefaultGroupsDir(), $this->configuration->getResultPath());

        // check that list files in getDefaultGroupsDir() === list files in getResultPath()
        $countResultGroups = FileSystemHelper::getCountNotEmptyFilesInDir($this->configuration->getResultPath());
        if ($countResultGroups !== $countDefaultGroups) {
            throw new NeededCountGroupsAndCountResultGroupsNotEqual(
                sprintf('Needed count groups and count result groups not equal. Expected - %d, got - %d.',
                    $countDefaultGroups,
                    $countResultGroups
                )
            );
        }
    }
}