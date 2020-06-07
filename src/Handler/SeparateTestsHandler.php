<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Strategy\FilePath\ItemTestCollectionBuilderInterface;
use TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface;

class SeparateTestsHandler
{
    /**
     * @var ItemTestCollectionBuilderInterface
     */
    private $fileSystemHelper;

    /**
     * @var \TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface
     */
    private $timeCounterStrategy;

    /**
     * @var string
     */
    private $resultPath;

    /**
     * @param ItemTestCollectionBuilderInterface $fileSystemHelper
     * @param \TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface $timeCounterStrategy
     * @param string $resultPath
     */
    public function __construct(
        ItemTestCollectionBuilderInterface $fileSystemHelper,
        DepthLevelStrategyInterface $timeCounterStrategy,
        string $resultPath
    ) {
        $this->fileSystemHelper    = $fileSystemHelper;
        $this->timeCounterStrategy = $timeCounterStrategy;
        $this->resultPath          = $resultPath;
    }

    public function buildTestInfoCollection(): array
    {
        return $this->fileSystemHelper->buildTestInfoCollection();
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
     */
    public function separateDirectoriesByTime(array $timeResults, int $countSuit): array
    {
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

    // TODO move to file_helper
    public function removeAllGroupFiles(): void
    {
        $files = scandir($this->resultPath); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $this->resultPath . $file;
            if (is_file($filePath)) {
                unlink($filePath); // delete file
            }
        }
    }

    public function getGroupDirectoryPath(): string
    {
        return $this->resultPath;
    }
}
