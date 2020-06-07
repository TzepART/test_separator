<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use Symfony\Component\Finder\Finder;
use TestSeparator\Model\ItemTestInfo;

class ItemTestCollectionBuilderByAllureReports extends AbstractItemTestCollectionBuilder
{
    /**
     * @var string
     */
    private $allureReportsDirectory;

    /**
     * @var array
     */
    private $testSuitesDirectories;

    /**
     * @var array
     */
    private $testsInfoMap = [];

    /**
     * @var Finder
     */
    private $fileFinder;

    /**
     * ItemTestCollectionBuilderByAllureReports constructor.
     *
     * @param string $baseTestDirPath
     * @param string $allureReportsDirectory
     * @param array $testSuitesDirectories
     */
    public function __construct(string $baseTestDirPath, string $allureReportsDirectory, array $testSuitesDirectories)
    {
        parent::__construct($baseTestDirPath);
        $this->allureReportsDirectory = $allureReportsDirectory;
        $this->testSuitesDirectories  = $testSuitesDirectories;
        $this->fileFinder             = new Finder();
    }

    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array
    {
        $this->buildTestInfoMap();
        $filePaths = $this->getFilePathsByDirectory($this->allureReportsDirectory);

        $results = [];
        foreach ($filePaths as $filePath) {
            echo $filePath . PHP_EOL;
            $xml = simplexml_load_string(file_get_contents($filePath));

            preg_match('/Support\.([a-z]+)/', (string) $xml->name, $suitMatches);
            $relativeParentDirectoryPath = $suitMatches[1];
            foreach ($xml->{'test-cases'}->children() as $child) {
                preg_match('/([^ ]+)/', (string) $child->name, $matches);
                $testName      = $matches[1];
                $timeExecuting = (int) ($child->attributes()->stop - $child->attributes()->start);
                $testKey       = sprintf('%s:%s', $relativeParentDirectoryPath, $testName);
                $testFilePath  = $this->testsInfoMap[$testKey];
                if ($testFilePath !== '') {
                    $relativeTestFilePath = str_replace($this->getBaseTestDirPath(), 'tests/', $testFilePath);
                    $results[]            = new ItemTestInfo(
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

    private function buildTestInfoMap(): void
    {
        foreach ($this->testSuitesDirectories as $index => $testSuitesDirectory) {
            $filePaths = $this->fileFinder->files()->in($this->getBaseTestDirPath() . $testSuitesDirectory . '/');

            foreach ($filePaths as $filePath) {
                $testFilePath = $filePath->getRealPath();
                preg_match_all('/public function (test[^(]+)/', file_get_contents($testFilePath), $matches);

                if (isset($matches[1]) && is_array($matches[1])) {
                    foreach ($matches[1] as $testName) {
                        $testKey                      = sprintf('%s:%s', $testSuitesDirectory, $testName);
                        $this->testsInfoMap[$testKey] = $testFilePath;
                    }
                }
            }
        }

    }
}
