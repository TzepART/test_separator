<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

use Psr\Log\LoggerAwareTrait;
use TestSeparator\Model\ItemTestInfo;

class DepthClassLevelStrategy implements DepthLevelStrategyInterface
{
    use LoggerAwareTrait;

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
            if (isset($timeResults[$testInfoItem->getRelativeTestFilePath()])) {
                $timeResults[$testInfoItem->getRelativeTestFilePath()] = round($timeResults[$testInfoItem->getRelativeTestFilePath()] + $time, 2);
            } else {
                $timeResults[$testInfoItem->getRelativeTestFilePath()] = $time;
            }
        }

        return $timeResults;
    }
}
