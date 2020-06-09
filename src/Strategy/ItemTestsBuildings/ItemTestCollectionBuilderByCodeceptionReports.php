<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use TestSeparator\Model\ItemTestInfo;
use TestSeparator\Service\FileSystemHelper;

class ItemTestCollectionBuilderByCodeceptionReports extends AbstractItemTestCollectionBuilder
{
    /**
     * @var string
     */
    private $codeceptionReportDir;

    /**
     * ItemTestCollectionBuilderByCodeceptionReports constructor.
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
        $filePaths = FileSystemHelper::getFilePathsByDirectory($this->codeceptionReportDir);

        $results = [];
        foreach ($filePaths as $filePath) {
            //TODO add catching if xml is Invalid
            $xml = simplexml_load_string(file_get_contents($filePath));

            foreach ($xml->{'testsuite'}->children() as $testChild) {
                preg_match('/([^ ]+)/', (string) $testChild->attributes()->name, $matches);
                $testName                    = $matches[1];
                $timeExecuting               = (int) (((float) $testChild->attributes()->time) * 1000);
                $testFilePath                = (string) $testChild->attributes()->file;
                $relativeTestFilePath        = preg_replace('/^.+tests\//', 'tests/', $testFilePath);
                $relativeParentDirectoryPath = preg_replace('/[A-z0-9]+\.php$/', '', $relativeTestFilePath);

                $results[] = new ItemTestInfo(
                    $relativeParentDirectoryPath,
                    $testFilePath,
                    $relativeTestFilePath,
                    $testName,
                    $timeExecuting
                );
            }
        }

        return $results;
    }
}
