<?php
declare(strict_types=1);

namespace TestSeparator\Strategy;

use TestSeparator\Model\TestInfo;

interface LevelDeepStrategyInterface
{
    /**
     * @param array|TestInfo[] $testInfoItems
     *
     * @return array
     */
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array;
}
