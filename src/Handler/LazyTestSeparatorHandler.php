<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Psr\Log\LoggerInterface;
use TestSeparator\Configuration;
use TestSeparator\Exception\NeededCountGroupsAndCountDefaultGroupsNotEqual;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Service\Validator\SeparatedEntityValidator;

class LazyTestSeparatorHandler implements TestsSeparatorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SeparatedEntityValidator
     */
    private $separatedEntityValidator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Configuration $configuration,
        SeparatedEntityValidator $separatedEntityValidator,
        LoggerInterface $logger
    )
    {
        $this->configuration = $configuration;
        $this->separatedEntityValidator = $separatedEntityValidator;
        $this->logger = $logger;
    }

    public function separateTests(int $countSuit): void
    {
        $defaultGroupsDir = $this->configuration->getDefaultGroupsDir();
        $resultGroupsDir = $this->configuration->getResultPath();

        // validate that count files in $this->configuration->getDefaultGroupsDir() === $countSuit
        $countDefaultGroups = FileSystemHelper::getCountNotEmptyFilesInDir($defaultGroupsDir);
        if ($countDefaultGroups !== $countSuit) {
            throw new NeededCountGroupsAndCountDefaultGroupsNotEqual(
                sprintf('Needed count groups and count default groups not equal. Expected - %d, got - %d.',
                    $countSuit,
                    $countDefaultGroups
                )
            );
        }

        // copy all files from $defaultGroupsDir to $this->configuration->getResultPath()
        $filePaths = FileSystemHelper::getFilePathsByDirectory($defaultGroupsDir);
        foreach ($filePaths as $fileName => $filePath) {
            $contentArray = file($filePath);
            $resultFilePath = $resultGroupsDir . $fileName;
            foreach ($contentArray as $separatedEntity) {
                if ($this->separatedEntityValidator->validateSeparatedEntity($separatedEntity)) {
                    file_put_contents($resultFilePath, $separatedEntity, FILE_APPEND);
                } else {
                    $this->logger->warning(sprintf('Entity %s in file %s is invalid.', $separatedEntity, $filePath));
                }
            }
        }

        // check that list files in getDefaultGroupsDir() === list files in getResultPath()
        $countResultGroups = FileSystemHelper::getCountNotEmptyFilesInDir($resultGroupsDir);
        if ($countResultGroups !== $countDefaultGroups) {
            $this->logger->warning(
                sprintf('Needed count groups and count result groups not equal. Expected - %d, got - %d.',
                    $countDefaultGroups,
                    $countResultGroups
                )
            );
        }
    }
}