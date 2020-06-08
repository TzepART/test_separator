<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

abstract class AbstractItemTestCollectionBuilder implements ItemTestCollectionBuilderInterface
{
    /**
     * @var string
     */
    private $baseTestDirPath;

    /**
     * AbstractItemTestCollectionBuilder constructor.
     *
     * @param string $baseTestDirPath
     */
    public function __construct(string $baseTestDirPath)
    {
        $this->baseTestDirPath = $baseTestDirPath;
    }

    /**
     * @return string
     */
    public function getBaseTestDirPath(): string
    {
        return $this->baseTestDirPath;
    }
}
