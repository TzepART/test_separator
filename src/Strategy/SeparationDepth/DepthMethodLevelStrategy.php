<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

use TestSeparator\Model\ItemTestInfo;

class DepthMethodLevelStrategy implements DepthLevelStrategyInterface
{
    /**
     * @param ItemTestInfo[]|array $testInfoItems
     *
     * @return array
     */
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array
    {
        $timeResults = [];

        /** @var ItemTestInfo $testInfoItem */
        foreach ($testInfoItems as $testInfoItem) {
            $time = round(($testInfoItem->getTimeExecuting()) / 1000, 2);
            $key  = $testInfoItem->getRelativeTestFilePath() . ':' . $testInfoItem->getTestName();
            if (isset($timeResults[$key])) {
                $timeResults[$key] = round($timeResults[$key] + $time, 2);
            } else {
                $timeResults[$key] = $time;
            }
        }

        return $timeResults;
    }
}
