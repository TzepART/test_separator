<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\FilePath;

use TestSeparator\Model\ItemTestInfo;

class ItemTestCollectionBuilderByByCodeceptionReports extends AbstractItemTestCollectionBuilder
{
    /**
     * @var string
     */
    private $codeceptionReportDir;

    /**
     * ItemTestCollectionBuilderByByCodeceptionReports constructor.
     *
     * @param string $baseTestDirPath
     * @param string $codeceptionReportDir
     */
    public function __construct(string $baseTestDirPath, string $codeceptionReportDir)
    {
        parent::__construct($baseTestDirPath);
        $this->codeceptionReportDir = $codeceptionReportDir;
    }

    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array
    {
        // TODO: Implement buildTestInfoCollection() method.
    }
}
