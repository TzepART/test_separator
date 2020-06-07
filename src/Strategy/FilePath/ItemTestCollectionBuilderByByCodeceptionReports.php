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
     * @param string $codeceptionReportDir
     */
    public function __construct(string $codeceptionReportDir)
    {
        $this->codeceptionReportDir = $codeceptionReportDir;
    }

    public function buildTestInfoCollection(): array
    {
        // TODO: Implement buildTestInfoCollection() method.
    }
}
