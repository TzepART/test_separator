<?php
declare(strict_types=1);

namespace TestSeparator\Strategy;

use TestSeparator\Model\TestInfo;

class DirectoryDeepStrategyService implements LevelDeepStrategyInterface
{
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array
    {
        $timeResults = [];

        /** @var TestInfo $testInfoItem */
        foreach ($testInfoItems as $testInfoItem) {
            $keyDir = preg_replace('/[A-z0-9]+\.php$/', '', $testInfoItem->getRelativePath());
            $time   = round(((int) $testInfoItem->getTime()) / 1000, 2);
            if (isset($timeResults[$keyDir])) {
                $timeResults[$keyDir] = round($timeResults[$keyDir] + $time, 2);
            } else {
                $timeResults[$keyDir] = $time;
            }
        }

        return $timeResults;
    }
}
