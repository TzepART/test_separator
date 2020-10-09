<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use TestSeparator\Model\ItemTestInfo;
use TestSeparator\Service\FileSystemHelper;
use \SimpleXMLElement;

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
            $xml = new SimpleXMLElement(file_get_contents($filePath));

            foreach ($xml->testsuite as $suiteChild) {
                foreach ($suiteChild->testcase as $testChild) {
                    preg_match('/([^ ]+)/', (string)$testChild['name'], $matches);
                    $testName = $matches[1];
                    $timeExecuting = (int)(((float)$testChild['time']) * 1000);
                    $testFilePath = (string)$testChild['file'];
                    $relativeTestFilePath = preg_replace('/^.+tests\//', 'tests/', $testFilePath);
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
        }

        return $results;
    }
}
