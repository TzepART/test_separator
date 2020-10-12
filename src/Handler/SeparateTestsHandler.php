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

class SeparateTestsHandler
{
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
    )
    {
        $this->itemTestCollectionBuilder = $itemTestCollectionBuilder;
        $this->timeCounterStrategy = $timeCounterStrategy;
        $this->resultPath = $resultPath;
        $this->logger = $logger;
        $this->itemTestCollectionBuilder->setLogger($logger);
        $this->timeCounterStrategy->setLogger($logger);
    }

    public function buildTestInfoCollection(): array
    {
        return $this->itemTestCollectionBuilder->buildTestInfoCollection();
    }

    public function groupTimeEntityWithCountedTime($testInfoItems): array
    {
        return $this->timeCounterStrategy->groupTimeEntityWithCountedTime($testInfoItems);
    }

    /**
     * @param array $timeResults
     * @param int $countSuit
     *
     * @return array
     * TODO add test
     */
    public function separateDirectoriesByTime(array $timeResults, int $countSuit): array
    {
        $timeResults = $this->updateForUniqueValues($timeResults);

        $greedy = new Greedy();
        $greedy->setData($timeResults);
        $greedy->setSize($countSuit);
        $result = $greedy->getResult();

        $groupBlockInfoItems = [];

        foreach ($result as $key => $block) {
            $groupBlockInfo = new GroupBlockInfo();
            foreach ($block as $time) {
                $keyDir = array_search($time, $timeResults, true);
                $groupBlockInfo->addDirTime($keyDir, $time);
            }
            $groupBlockInfo->setSummTime(array_sum($block));
            $groupBlockInfoItems[] = $groupBlockInfo;
        }

        return $groupBlockInfoItems;
    }

    public function removeAllGroupFiles(): void
    {
        FileSystemHelper::removeAllFilesInDirectory($this->getGroupDirectoryPath());
    }

    public function getGroupDirectoryPath(): string
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
