<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use Symfony\Component\Finder\Finder;
use TestSeparator\Model\ItemTestInfo;
use TestSeparator\Service\FileSystemHelper;

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
        $filePaths = FileSystemHelper::getFilePathsByDirectory($this->allureReportsDirectory);

        $results = [];
        foreach ($filePaths as $filePath) {
            $xml = simplexml_load_string(file_get_contents($filePath));

            preg_match('/Support\.([a-z]+)/', (string) $xml->name, $suitMatches);
            $relativeParentDirectoryPath = $suitMatches[1];
            foreach ($xml->{'test-cases'}->children() as $child) {
                preg_match('/([^ ]+)/', (string) $child->name, $matches);
                $testName      = $matches[1];
                $timeExecuting = (int) ($child->attributes()->stop - $child->attributes()->start);
                $testKey       = sprintf('%s:%s', $relativeParentDirectoryPath, $testName);
                if (isset($this->testsInfoMap[$testKey]) && $this->testsInfoMap[$testKey]['path'] !== '') {
                    $results[] = new ItemTestInfo(
                        $relativeParentDirectoryPath,
                        $this->testsInfoMap[$testKey]['path'],
                        $this->testsInfoMap[$testKey]['relative-path'],
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
            $parentDir = $this->getBaseTestDirPath() . $testSuitesDirectory . '/';
            $filePaths = $this->fileFinder->files()->in($parentDir);

            foreach ($filePaths as $filePath) {
                $testFilePath = $filePath->getRealPath();
                preg_match_all('/public function (test[^(]+)/', file_get_contents($testFilePath), $matches);

                if (isset($matches[1]) && is_array($matches[1])) {
                    foreach ($matches[1] as $testName) {
                        $testKey                      = sprintf('%s:%s', $testSuitesDirectory, $testName);
                        $this->testsInfoMap[$testKey] = [
                            'path'          => $testFilePath,
                            'relative-path' => $parentDir . $filePath->getRelativePathname(),
                        ];
                    }
                }
            }
        }

    }
}
