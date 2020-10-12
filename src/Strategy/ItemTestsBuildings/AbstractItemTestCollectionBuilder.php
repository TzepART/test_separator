<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractItemTestCollectionBuilder implements ItemTestCollectionBuilderInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $baseTestDirPath;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
