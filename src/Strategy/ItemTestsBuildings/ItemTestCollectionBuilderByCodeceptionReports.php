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
     * @var array
     */
    private $availableSuitesDirectories;

    /**
     * ItemTestCollectionBuilderByCodeceptionReports constructor.
     *
     * @param string $baseTestDirPath
     * @param string $codeceptionReportDir
     * @param array $testSuitesDirectories
     */
    public function __construct(string $baseTestDirPath, string $codeceptionReportDir, array $testSuitesDirectories)
    {
        parent::__construct($baseTestDirPath);
        $this->codeceptionReportDir = $codeceptionReportDir;
        $this->availableSuitesDirectories = array_map(function (string $shortPath) {
            return 'tests/' . $shortPath . '/';
        }, $testSuitesDirectories);
    }

    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array
    {
        $filePaths = FileSystemHelper::getFilePathsByDirectory($this->codeceptionReportDir);

        $results = [];
        foreach ($filePaths as $filePath) {
            $this->logger->info(sprintf('File %s is processed.', $filePath));

            $xml = simplexml_load_string(file_get_contents($filePath));
            if (!$xml) {
                $this->logger->notice(sprintf('File %s could not be parsed as XML.', $filePath));
                continue;
            }

            foreach ($xml->testsuite as $suiteChild) {
                foreach ($suiteChild->testcase as $testChild) {
                    $testFilePath = (string)$testChild['file'];
                    if ($this->checkDirectoryAvailable($testFilePath)) {
                        preg_match('/([^ ]+)/', (string)$testChild['name'], $matches);
                        $testName = $matches[1];
                        $timeExecuting = (int)(((float)$testChild['time']) * 1000);
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
        }

        return $results;
    }

    private function checkDirectoryAvailable(string $testFilePath): bool
    {
        foreach ($this->availableSuitesDirectories as $testSuitesDirectory) {
            if (stripos($testFilePath, $testSuitesDirectory) > 0) {
                return true;
            }
        }

        return false;
    }
}
