<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

class DepthMethodLevelStrategy implements DepthLevelStrategyInterface
{
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array
    {
        $timeResults = [];

        foreach ($testInfoItems as $testInfoItem) {
            $time = round(($testInfoItem->getTime()) / 1000, 2);
            $key  = $testInfoItem->getRelativePath() . ':' . $testInfoItem->getTest();
            if (isset($timeResults[$key])) {
                $timeResults[$key] = round($timeResults[$key] + $time, 2);
            } else {
                $timeResults[$key] = $time;
            }
        }

        return $timeResults;
    }
}
