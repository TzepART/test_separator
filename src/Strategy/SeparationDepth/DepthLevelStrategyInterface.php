<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\SeparationDepth;

use TestSeparator\Model\ItemTestInfo;

interface DepthLevelStrategyInterface
{
    /**
     * @param ItemTestInfo[]|array $testInfoItems
     *
     * @return array
     */
    public function groupTimeEntityWithCountedTime(array $testInfoItems): array;
}
