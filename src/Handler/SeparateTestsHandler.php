<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderInterface;
use TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface;

class SeparateTestsHandler
{
    /**
     * @var ItemTestCollectionBuilderInterface
     */
    private $itemTestCollectionBuilder;

    /**
     * @var DepthLevelStrategyInterface
     */
    private $timeCounterStrategy;

    /**
     * @var string
     */
    private $resultPath;

    /**
     * @param ItemTestCollectionBuilderInterface $itemTestCollectionBuilder
     * @param DepthLevelStrategyInterface $timeCounterStrategy
     * @param string $resultPath
     */
    public function __construct(
        ItemTestCollectionBuilderInterface $itemTestCollectionBuilder,
        DepthLevelStrategyInterface $timeCounterStrategy,
        string $resultPath
    ) {
        $this->itemTestCollectionBuilder = $itemTestCollectionBuilder;
        $this->timeCounterStrategy = $timeCounterStrategy;
        $this->resultPath = $resultPath;
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
