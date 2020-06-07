<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use TestSeparator\Model\ItemTestInfo;

interface ItemTestCollectionBuilderInterface
{
    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array;

    public function getBaseTestDirPath(): string;
}
