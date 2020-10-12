<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

use Psr\Log\LoggerAwareTrait;
use TestSeparator\Model\ItemTestInfo;

class DepthDirectoryLevelStrategy implements DepthLevelStrategyInterface
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
            $keyDir = preg_replace('/[A-z0-9]+\.php$/', '', $testInfoItem->getRelativeTestFilePath());
            $time = round(((int)$testInfoItem->getTimeExecuting()) / 1000, 2);
            if (isset($timeResults[$keyDir])) {
                $timeResults[$keyDir] = round($timeResults[$keyDir] + $time, 2);
            } else {
                $timeResults[$keyDir] = $time;
            }
        }

        return $timeResults;
    }
}
