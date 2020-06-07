<?php
declare(strict_types=1);

namespace TestSeparator\Strategy;

use TestSeparator\Model\ItemTestInfo;

interface LevelDeepStrategyInterface
{
    /**
     * @param array|ItemTestInfo[] $testInfoItems
     *
     * @return array
     */
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array;
}
