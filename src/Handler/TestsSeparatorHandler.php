<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderInterface;
use TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface;

class TestsSeparatorHandler implements TestsSeparatorInterface
{
    /**
     * @var string
     */
    private $groupPrefix = 'time_group_';

    /**
     * @var ItemTestCollectionBuilderInterface&LoggerAwareTrait
     */
    private $itemTestCollectionBuilder;

    /**
     * @var DepthLevelStrategyInterface&LoggerAwareTrait
     */
    private $timeCounterStrategy;

    /**
     * @var string
     */
    private $resultPath;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ItemTestCollectionBuilderInterface&LoggerAwareTrait $itemTestCollectionBuilder
     * @param DepthLevelStrategyInterface&LoggerAwareTrait $timeCounterStrategy
     * @param string $resultPath
     * @param LoggerInterface $logger
     */
    public function __construct(
        ItemTestCollectionBuilderInterface $itemTestCollectionBuilder,
        DepthLevelStrategyInterface $timeCounterStrategy,
        string $resultPath,
        LoggerInterface $logger
    ) {
        $this->itemTestCollectionBuilder = $itemTestCollectionBuilder;
        $this->timeCounterStrategy = $timeCounterStrategy;
        $this->resultPath = $resultPath;
        $this->logger = $logger;
        $this->itemTestCollectionBuilder->setLogger($logger);
        $this->timeCounterStrategy->setLogger($logger);
    }

    /**
     * @param int $countSuit
     */
    public function separateTests(int $countSuit): void
    {
        $testInfoCollection = $this->buildTestInfoCollection();
        $entityWithTime = $this->groupTimeEntityWithCountedTime($testInfoCollection);
        $arGroups = $this->separateDirectoriesByTime($entityWithTime, $countSuit);

        // remove all group files
        $this->removeAllGroupFiles();

        /** @var GroupBlockInfo $arGroupBlockInfo */
        foreach ($arGroups as $groupNumber => $arGroupBlockInfo) {
            $groupName = $this->groupPrefix . $groupNumber;
            $filePath = $this->getGroupDirectoryPath() . $groupName . '.txt';

            foreach ($arGroupBlockInfo->getDirTimes() as $localTestsDir => $time) {
                file_put_contents($filePath, $localTestsDir . PHP_EOL, FILE_APPEND);
            }

            if (file_exists($filePath)) {
                if (!filesize($filePath)) {
                    $this->logger->info(sprintf('File for %s is empty.', $groupName));
                } else {
                    $this->logger->info(sprintf('File for %s was created successfully.', $groupName));
                }
            } else {
                $this->logger->notice(sprintf('File for %s doesn\'t exist.', $groupName));
            }
        }
    }

    private function buildTestInfoCollection(): array
    {
        $this->logger->info(sprintf('ItemTestCollectionBuilder - %s', get_class($this->itemTestCollectionBuilder)));
        return $this->itemTestCollectionBuilder->buildTestInfoCollection();
    }

    private function groupTimeEntityWithCountedTime($testInfoItems): array
    {
        $this->logger->info(sprintf('DepthLevelStrategy - %s', get_class($this->timeCounterStrategy)));
        return $this->timeCounterStrategy->groupTimeEntityWithCountedTime($testInfoItems);
    }

    /**
     * @param array $timeResults
     * @param int $countSuit
     *
     * @return array
     * TODO add test
     */
    private function separateDirectoriesByTime(array $timeResults, int $countSuit): array
    {
        $timeResults = $this->updateForUniqueValues($timeResults);

        $greedy = new Greedy();
        $greedy->setData($timeResults);
        $greedy->setSize($countSuit);
        $result = $greedy->getResult();

        $groupBlockInfoItems = [];

        $blockNumber = 0;
        foreach ($result as $key => $block) {
            $groupBlockInfo = new GroupBlockInfo();
            foreach ($block as $time) {
                $keyDir = array_search($time, $timeResults, true);
                $groupBlockInfo->addDirTime($keyDir, $time);
            }

            $groupBlockInfo->setSummTime(array_sum($block));
            $groupBlockInfoItems[$blockNumber] = $groupBlockInfo;

            $this->logger->info(sprintf('Block %d: summ time - %f; count items - %d;', $blockNumber, $groupBlockInfo->getSummTime(), count($block)));
            $blockNumber++;
        }

        return $groupBlockInfoItems;
    }

    private function removeAllGroupFiles(): void
    {
        FileSystemHelper::removeAllFilesInDirectory($this->getGroupDirectoryPath());
    }

    private function getGroupDirectoryPath(): string
    {
        return $this->resultPath;
    }

    private function updateForUniqueValues(array $timeResults): array
    {
        $updateValues = [];
        $initValue = 0.000001;
        $multiplier = 1;
        foreach ($timeResults as $key => $timeResult) {
            $updateValues[$key] = $timeResult + $initValue * $multiplier;
            $multiplier++;
        }

        return $updateValues;
    }
}
