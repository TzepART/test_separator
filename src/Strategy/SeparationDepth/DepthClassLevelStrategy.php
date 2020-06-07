<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

class DepthClassLevelStrategy implements DepthLevelStrategyInterface
{
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array
    {
        $timeResults = [];

        foreach ($testInfoItems as $testInfoItem) {
            $time = round(($testInfoItem->getTime()) / 1000, 2);
            if (isset($timeResults[$testInfoItem->getRelativePath()])) {
                $timeResults[$testInfoItem->getRelativePath()] = round($timeResults[$testInfoItem->getRelativePath()] + $time, 2);
            } else {
                $timeResults[$testInfoItem->getRelativePath()] = $time;
            }
        }

        return $timeResults;
    }
}
